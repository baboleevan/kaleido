<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Pagination extends CI_Pagination {

	protected $display_always		= FALSE; // Display pagination even it has one page only.
	protected $use_fixed_page		= FALSE; // Use a fixed number of pages.
	protected $fixed_page_num		= 5; // A fixed number of pages on the pagination.
	protected $disable_first_link	= FALSE; // Disable the first link when the current page is the first page.
	protected $disable_last_link	= FALSE; // Disable the last link when the current page is the last page.
	protected $disable_prev_link	= FALSE; // Disable the previous link when the current page is the first page.
	protected $disable_next_link	= FALSE; // Disable the next link when the current page is the last page.
	protected $display_first_always	= TRUE; // Always display the first link even the current page is the first page.
	protected $display_last_always	= TRUE; // Always display the last link even the current page is the last page.
	protected $display_prev_always	= FALSE; // Always display the previous link even the current page is the first page.
	protected $display_next_always	= FALSE; // Always display the next link even the current page is the last page.
	
	protected $disabled_first_tag_open	= '';
	protected $disabled_first_tag_close	= '&nbsp;';
	protected $disabled_last_tag_open	= '&nbsp;';
	protected $disabled_last_tag_close	= '';
	protected $disabled_prev_tag_open	= '&nbsp;';
	protected $disabled_prev_tag_close	= '';
	protected $disabled_next_tag_open	= '&nbsp;';
	protected $disabled_next_tag_close	= '&nbsp;';

	public function __construct($params = array())
	{
		parent::__construct($params);
	}

	public function create_links()
	{
		// If our item count or per-page total is zero there is no need to continue.
		// Note: DO NOT change the operator to === here!
		if ($this->total_rows == 0 OR $this->per_page == 0)
		{
			return '';
		}

		// Calculate the total number of pages
		$num_pages = (int) ceil($this->total_rows / $this->per_page);

		// Is there only one page? Hm... nothing more to do here then.
		if ($this->display_always === FALSE AND $num_pages === 1)
		{
			return '';
		}

		// Check the user defined number of links.
		$this->num_links = (int) $this->num_links;

		if ($this->num_links < 0)
		{
			show_error('Your number of links must be a non-negative number.');
		}

		// Keep any existing query string items.
		// Note: Has nothing to do with any other query string option.
		if ($this->reuse_query_string === TRUE)
		{
			$get = $this->CI->input->get();

			// Unset the controll, method, old-school routing options
			unset($get['c'], $get['m'], $get[$this->query_string_segment]);
		}
		else
		{
			$get = array();
		}

		// Put together our base and first URLs.
		// Note: DO NOT append to the properties as that would break successive calls
		$base_url = trim($this->base_url);
		$first_url = $this->first_url;

		$query_string = '';
		$query_string_sep = (strpos($base_url, '?') === FALSE) ? '?' : '&amp;';

		// Are we using query strings?
		if ($this->page_query_string === TRUE)
		{
			// If a custom first_url hasn't been specified, we'll create one from
			// the base_url, but without the page item.
			if ($first_url === '')
			{
				$first_url = $base_url;

				// If we saved any GET items earlier, make sure they're appended.
				if ( ! empty($get))
				{
					$first_url .= $query_string_sep.http_build_query($get);
				}
			}

			// Add the page segment to the end of the query string, where the
			// page number will be appended.
			$base_url .= $query_string_sep.http_build_query(array_merge($get, array($this->query_string_segment => '')));
		}
		else
		{
			// Standard segment mode.
			// Generate our saved query string to append later after the page number.
			if ( ! empty($get))
			{
				$query_string = $query_string_sep.http_build_query($get);
				$this->suffix .= $query_string;
			}

			// Does the base_url have the query string in it?
			// If we're supposed to save it, remove it so we can append it later.
			if ($this->reuse_query_string === TRUE && ($base_query_pos = strpos($base_url, '?')) !== FALSE)
			{
				$base_url = substr($base_url, 0, $base_query_pos);
			}

			if ($first_url === '')
			{
				$first_url = $base_url.$query_string;
			}

			$base_url = rtrim($base_url, '/').'/';
		}

		// Determine the current page number.
		$base_page = ($this->use_page_numbers) ? 1 : 0;

		// Are we using query strings?
		if ($this->page_query_string === TRUE)
		{
			$this->cur_page = $this->CI->input->get($this->query_string_segment);
		}
		else
		{
			// Default to the last segment number if one hasn't been defined.
			if ($this->uri_segment === 0)
			{
				$this->uri_segment = count($this->CI->uri->segment_array());
			}

			$this->cur_page = $this->CI->uri->segment($this->uri_segment);

			// Remove any specified prefix/suffix from the segment.
			if ($this->prefix !== '' OR $this->suffix !== '')
			{
				$this->cur_page = str_replace(array($this->prefix, $this->suffix), '', $this->cur_page);
			}
		}

		// If something isn't quite right, back to the default base page.
		if ( ! ctype_digit($this->cur_page) OR ($this->use_page_numbers && (int) $this->cur_page === 0))
		{
			$this->cur_page = $base_page;
		}
		else
		{
			// Make sure we're using integers for comparisons later.
			$this->cur_page = (int) $this->cur_page;
		}

		// Is the page number beyond the result range?
		// If so, we show the last page.
		if ($this->use_page_numbers)
		{
			if ($this->cur_page > $num_pages)
			{
				$this->cur_page = $num_pages;
			}
		}
		elseif ($this->cur_page > $this->total_rows)
		{
			$this->cur_page = ($num_pages - 1) * $this->per_page;
		}

		$uri_page_number = $this->cur_page;

		// If we're using offset instead of page numbers, convert it
		// to a page number, so we can generate the surrounding number links.
		if ( ! $this->use_page_numbers)
		{
			$this->cur_page = (int) floor(($this->cur_page/$this->per_page) + 1);
		}

		// Calculate the start and end numbers. These determine
		// which number to start and end the digit links with.
		if ($this->use_fixed_page)
		{
			$start = (ceil($this->cur_page / $this->fixed_page_num) - 1) * $this->fixed_page_num + 1 + 1; // Last plus one is for loop statement starts $start - 1 
			$end   = (ceil($this->cur_page / $this->fixed_page_num) == ceil($num_pages / $this->fixed_page_num)) ? $num_pages : ceil($this->cur_page / $this->fixed_page_num) * $this->fixed_page_num;
		}
		else
		{
			$start	= (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
			$end	= (($this->cur_page + $this->num_links) < $num_pages) ? ($this->cur_page <= $this->num_links ? $this->num_links * 2 : $this->cur_page + $this->num_links - 1) : $num_pages;
			$start = $end - $start < $this->num_links * 2 ? ($end - $this->num_links * 2) + 1 : $start;
		}
		

		// And here we go...
		$output = '';

		// Render the "First" link.
		if ($this->first_link !== FALSE)
		{
			if ($this->display_first_always === TRUE AND $this->disable_first_link === TRUE AND $this->cur_page == 1)
			{
				$output .= $this->disabled_first_tag_open.$this->first_link.$this->disabled_first_tag_close;
			}
			else if ($this->display_first_always === TRUE OR $this->cur_page != 1)
			{
				// Take the general parameters, and squeeze this pagination-page attr in for JS frameworks.
				$attributes = sprintf('%s %s="%d"', $this->_attributes, $this->data_page_attr, 1);

				$output .= $this->first_tag_open.'<a href="'.$first_url.'"'.$attributes.$this->_attr_rel('start').'>'.$this->first_link.'</a>'.$this->first_tag_close;
			}
		}

		// Render the "Previous" link.
		if ($this->prev_link !== FALSE)
		{
			if ($this->display_prev_always === TRUE AND $this->disable_prev_link === TRUE AND $this->cur_page == 1)
			{
				$output .= $this->disabled_prev_tag_open.$this->prev_link.$this->disabled_prev_tag_close;
			}
			else if ($this->display_prev_always === TRUE OR $this->cur_page != 1)
			{
				$i = ($this->use_page_numbers AND $first_url !== '' AND $uri_page_number == 1) ? 1 : (($this->use_page_numbers) ? $uri_page_number - 1 : $uri_page_number - $this->per_page);

				$attributes = sprintf('%s %s="%d"', $this->_attributes, $this->data_page_attr, (int) $i);

				if ($i === $base_page)
				{
					// First page
					$output .= $this->prev_tag_open.'<a href="'.$first_url.'"'.$attributes.$this->_attr_rel('prev').'>'
						.$this->prev_link.'</a>'.$this->prev_tag_close;
				}
				else
				{
					$append = $this->prefix.$i.$this->suffix;
					$output .= $this->prev_tag_open.'<a href="'.$base_url.$append.'"'.$attributes.$this->_attr_rel('prev').'>'
						.$this->prev_link.'</a>'.$this->prev_tag_close;
				}
			}
		}

		// Render the pages
		if ($this->display_pages !== FALSE)
		{
			// Write the digit links
			for ($loop = $start -1; $loop <= $end; $loop++)
			{
				$i = ($this->use_page_numbers) ? $loop : ($loop * $this->per_page) - $this->per_page;

				$attributes = sprintf('%s %s="%d"', $this->_attributes, $this->data_page_attr, (int) $i);

				if ($i >= $base_page)
				{
					if ($this->cur_page == $loop)
					{
						// Current page
						$output .= $this->cur_tag_open.$loop.$this->cur_tag_close;
					}
					elseif ($i == $base_page)
					{
						// First page
						$output .= $this->num_tag_open.'<a href="'.$first_url.'"'.$attributes.$this->_attr_rel('start').'>'
							.$loop.'</a>'.$this->num_tag_close;
					}
					else
					{
						$append = $this->prefix.$i.$this->suffix;
						$output .= $this->num_tag_open.'<a href="'.$base_url.$append.'"'.$attributes.$this->_attr_rel('start').'>'
							.$loop.'</a>'.$this->num_tag_close;
					}
				}
			}
		}

		// Render the "next" link
		if ($this->next_link !== FALSE)
		{
			if ($this->display_next_always === TRUE AND $this->disable_next_link === TRUE AND $this->cur_page == $num_pages)
			{
				$output .= $this->disabled_next_tag_open.$this->next_link.$this->disabled_next_tag_close;
			}
			else if ($this->display_next_always === TRUE OR $this->cur_page != $num_pages)
			{
				$i = ($this->use_page_numbers) ? (($this->cur_page == $num_pages)? $num_pages : $this->cur_page + 1) : $this->cur_page * $this->per_page;

				$attributes = sprintf('%s %s="%d"', $this->_attributes, $this->data_page_attr, (int) $i);

				$output .= $this->next_tag_open.'<a href="'.$base_url.$this->prefix.$i.$this->suffix.'"'.$attributes
					.$this->_attr_rel('next').'>'.$this->next_link.'</a>'.$this->next_tag_close;
			}
		}

		// Render the "Last" link
		if ($this->last_link !== FALSE)
		{
			if ($this->display_last_always === TRUE AND $this->disable_last_link === TRUE AND $this->cur_page == $num_pages)
			{
				$output .= $this->disabled_last_tag_open.$this->last_link.$this->disabled_last_tag_close;
			}
			else if ($this->display_last_always === TRUE OR $this->cur_page != $num_pages)
			{
				$i = ($this->use_page_numbers) ? $num_pages : ($num_pages * $this->per_page) - $this->per_page;

				$attributes = sprintf('%s %s="%d"', $this->_attributes, $this->data_page_attr, (int) $i);

				$output .= $this->last_tag_open.'<a href="'.$base_url.$this->prefix.$i.$this->suffix.'"'.$attributes.'>'.$this->last_link.'</a>'.$this->last_tag_close;
			}
		}

		// Kill double slashes. Note: Sometimes we can end up with a double slash
		// in the penultimate link so we'll kill all double slashes.
		$output = preg_replace('#([^:])//+#', '\\1/', $output);

		// Add the wrapper HTML if exists
		return $this->full_tag_open.$output.$this->full_tag_close;
	}

}