<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Meta -->
		<meta charset="utf-8">
		<meta name="apple-mobile-web-app-title" content="{{ @Core->getName() }}">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<meta name="description" content="{{ @description }}">
		<check if="{{ @home }} === false">
			<link rel="canonical" href="{{ @canonical }}">
		</check>
		<link rel="alternate" type="application/rss+xml" title="{{ @Core->getName() }}" href="{{ @feed }}">
		<link rel="search" type="application/opensearchdescription+xml" href="{{ @domain }}opensearchdescription.xml" title="{{ @Core->getName() }}">
		<title>{{ @title }}</title>
		
		<!-- Icons -->
		<link rel="apple-touch-icon" sizes="57x57" href="{{ @domain }}apple-touch-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="114x114" href="{{ @domain }}apple-touch-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="72x72" href="{{ @domain }}apple-touch-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="144x144" href="{{ @domain }}apple-touch-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="60x60" href="{{ @domain }}apple-touch-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="120x120" href="{{ @domain }}apple-touch-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="76x76" href="{{ @domain }}apple-touch-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="152x152" href="{{ @domain }}apple-touch-icon-152x152.png">
		<link rel="icon" sizes="196x196" type="image/png" href="{{ @domain }}favicon-196x196.png">
		<link rel="icon" sizes="160x160" type="image/png" href="{{ @domain }}favicon-160x160.png">
		<link rel="icon" sizes="96x96" type="image/png" href="{{ @domain }}favicon-96x96.png">
		<link rel="icon" sizes="32x32" type="image/png" href="{{ @domain }}favicon-32x32.png">
		<link rel="icon" sizes="16x16" type="image/png" href="{{ @domain }}favicon-16x16.png">
		<meta name="msapplication-TileColor" content="#ffffff">
		<meta name="msapplication-TileImage" content="{{ @domain }}mstile-144x144.png">
		
		<!-- Google+ -->
		<link rel="publisher" href="https://plus.google.com/{{ @gplus }}">
		
		<!-- Open Graph -->
		<meta property="og:image" content="{{ @domain }}img/pka.png">
		<meta property="og:site_name" content="{{ @Core->getName() }}">
		
		<include href="../views/meta.tpl" />
		
		<!-- CSS -->
		<link rel="stylesheet" type="text/css" href="{{ @domain }}css/main.css?ver={{ @css_modified_time }}">
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans:400,700">
		<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/3.0.2/css/font-awesome.min.css">
	</head>
	<body data-type="{{ @type }}">
		<aside class="sidebar">
			<nav id="sidebar">
				<div class="search-form"><input class="search-field" type="search" id="search-field" name="search" placeholder="Search"></div>
				<h3><span>Episodes</span><a title="Random Episode" href="<?php echo $domain; ?>episode/random" class="random-button timelined"><i class="icon-random"></i></a></h3>
				<div id="search-error" class="error">
					<p></p>
				</div>
				<ul data-current="<check if="{{ @type == 'episode' }}">{{ @current_episode->getIdentifier() }}</check>">
				<repeat group="{{ array_reverse(@Core->getEpisodes()) }}" value="{{ @episode }}">
					<li data-episode="{{ @episode->getIdentifier() }}"<check if="{{ @type == 'episode' }}"><check if="{{ @current_episode->getIdentifier() == @episode->getIdentifier() }}"> id="active"</check></check>>
						<a href="{{ @domain }}episode/{{ @episode->getNumber() }}" class="<check if="{{ @episode->getHighlighted() }}">highlighted-episode</check>"><span>#{{ @episode->getNumber() }}</span><check if="{{ @episode->getTimelined() === true }}"><span class="timelined"></span></check></a>
					</li>
				</repeat>
				</ul>
			</nav>
		</aside>
		<section class="main">
			<header>
				<a href="#" class="toggle-menu icon-reorder"></a>
				<h1>{{ @Core->getName() }}</h1>
			</header>
			<div id="container">
				<include href="{{ '../views/' . @type . '.tpl' }}" />
				<ul id="footer-links">
					<li><a href="{{ @domain }}credits">Developers and Contributors</a></li>
					<li><a href="{{ @domain }}feedback">Provide us with Feedback</a></li>
				</ul>
			</div>
		</section>
		<div id="loader"></div>
		<script type="application/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script type="application/javascript">var domain = "{{ @domain }}";var site_name = "{{ @Core->getName() }}";</script>
		<script type="application/javascript" src="<?php echo $domain; ?>js/main.js?ver={{ @js_modified_time }}"></script>
	</body>
</html>