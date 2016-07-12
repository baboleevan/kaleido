<!doctype html>
<head>
	<meta charset="utf-8">
	<title>Kaleido</title>
	<!-- Mobile specific metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<!-- Force IE9 to render in normal mode -->
	<!--[if IE]><meta http-equiv="x-ua-compatible" content="IE=9" /><![endif]-->
	
	<!-- Import google fonts - Heading first/ text second -->
	<link href='http://fonts.googleapis.com/css?family=Roboto:400,300,300italic,400italic,700' rel='stylesheet' type='text/css'>
	<!-- Css files -->
	<!-- Icons -->
	<link href="/assets/css/icons.css" rel="stylesheet" />
	<!-- Bootstrap stylesheets (included template modifications) -->
	<link href="/assets/css/bootstrap.css" rel="stylesheet" />
	<!-- jQueryUI -->
	<link href="/assets/css/appstart-theme/jquery.ui.all.css" rel="stylesheet" />
	<!-- Plugins stylesheets (all plugin custom css) -->
	<link href="/assets/css/plugins.css" rel="stylesheet" />
	<!-- Main stylesheets (template main css file) -->
	<link href="/assets/css/main.css" rel="stylesheet" />
	<!-- Custom stylesheets ( Put your own changes here ) -->
	<link href="/assets/css/custom.css" rel="stylesheet" />
	<!-- Fav and touch icons -->
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="/assets/img/ico/apple-touch-icon-144-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="/assets/img/ico/apple-touch-icon-114-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="/assets/img/ico/apple-touch-icon-72-precomposed.png">
	<link rel="apple-touch-icon-precomposed" href="/assets/img/ico/apple-touch-icon-57-precomposed.png">
	<link rel="icon" href="/assets/img/ico/favicon.ico" type="image/png">
	<!-- Windows8 touch icon ( http://www.buildmypinnedsite.com/ )-->
	<meta name="msapplication-TileColor" content="#3399cc" />

	<!-- Javascripts -->
        <!-- Important javascript libs(put in all pages) -->
        <script src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
        <script>window.jQuery || document.write('<script src="/assets/js/libs/jquery-2.1.1.min.js">\x3C/script>')</script>
        <script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
        <script>window.jQuery || document.write('<script src="/assets/js/libs/jquery-ui-1.10.4.min.js">\x3C/script>')</script>
		<!--[if lt IE 9]>
			<script type="text/javascript" src="/assets/js/libs/excanvas.min.js"></script>
			<script type="text/javascript" src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
			<script type="text/javascript" src="/assets/js/libs/respond.min.js"></script>
		<![endif]-->

</head>

<body class="ovh">
	<div id="preloader">
		<div id="preloader-logo">

		</div>
		<div id="preloader-icon">
			<i class="im-spinner icon-spin"></i>
		</div>
	</div>
	<!-- Start #header -->
	<div id="header">
		<div class="container-fluid">
			<div class="navbar">
				<div class="navbar-header">
					<!-- Show navigation toggle on phones -->
					<button id="showNav" class="navbar-toggle" type="button">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<!-- Show logo for large screens and laptops -->
					<a class="navbar-brand visible-lg visible-md" href="#">Kaleido</a>
					<!-- Show logo for small screens -->
					<a class="navbar-brand hidden-lg hidden-md hidden-xs" href="index.html">
						<img src="/assets/img/logo-sm.png" alt="Jump start">
					</a>
				</div>
				<nav id="top-nav" class="navbar-no-collapse" role="navigation">
					<!-- Navbar nav -->
					<ul class="nav navbar-nav pull-right">
						<!-- li class="dropdown">
                                <a href="#" data-toggle="dropdown">
                                    <i class="im-earth"></i>
                                    <i class="nav-notification im-circle2"></i>
                                    <span class="sr-only">Notifications</span>
                                </a>
                            </li -->
						<li class="dropdown">
							<a href="#" data-toggle="dropdown">
								<i class="im-earth"></i>
								<span class="sr-only">config</span>
							</a>
							<ul class="dropdown-menu dropdown-email right" role="menu">
								<!-- li><a href="#" class="dropdown-menu-header">설정</a></li -->
								<li class="divider"></li>
								<li class="clearfix">
									<a href="#">
										로그아웃<span class="time-ago"></span>
									</a>
								</li>
							</ul>
						</li>
					</ul>
				</nav>
			</div>
		</div>
	</div>
	<style>
		#header{background: #2C3E50;}
		#header .navbar{background: #2C3E50;}
		#sidebar .sidebar-inner .option-buttons #search-nav .form-group input {background-color: #FFFFFF}
	</style>