<?php

namespace Kaleido;
use Kaleido\Kaleido;

class Kaleido 
{
    public function __construct(array $config = [])
    {            
      print_r($config);
    }

	public function page()
    {
		echo "page return";
    }
   
}
