<?php

	require_once("config.php");
	require_once("class.person.php");
	
	if(isset($_GET["person"])){
		$Person = new Person($con);
		$Person->initWithName($_GET['person']);
	}
	if (isset($_SERVER["HTTP_USER_AGENT"]) && (strpos($_SERVER["HTTP_USER_AGENT"], "MSIE") !== false)) header("X-UA-Compatible: IE=edge,chrome=1");
	
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Meta -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<meta name="description" content="The article description.">
		<link rel="search" type="application/opensearchdescription+xml" href="<?php echo $domain; ?>opensearchdescription.xml" title="Painkiller Already Archive">
		<title>Person <?php echo $Person->getName(); ?> &middot; Painkiller Already Archive</title>
		
		<!-- Icons -->
		<link rel="apple-touch-icon" sizes="57x57" href="<?php echo $domain; ?>apple-touch-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="114x114" href="<?php echo $domain; ?>apple-touch-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="72x72" href="<?php echo $domain; ?>apple-touch-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="144x144" href="<?php echo $domain; ?>apple-touch-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="60x60" href="<?php echo $domain; ?>apple-touch-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="120x120" href="<?php echo $domain; ?>apple-touch-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="76x76" href="<?php echo $domain; ?>apple-touch-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="152x152" href="<?php echo $domain; ?>apple-touch-icon-152x152.png">
		<link rel="icon" sizes="196x196" type="image/png" href="<?php echo $domain; ?>favicon-196x196.png">
		<link rel="icon" sizes="160x160" type="image/png" href="<?php echo $domain; ?>favicon-160x160.png">
		<link rel="icon" sizes="96x96" type="image/png" href="<?php echo $domain; ?>favicon-96x96.png">
		<link rel="icon" sizes="32x32" type="image/png" href="<?php echo $domain; ?>favicon-32x32.png">
		<link rel="icon" sizes="16x16" type="image/png" href="<?php echo $domain; ?>favicon-16x16.png">
		<meta name="msapplication-TileColor" content="#ffffff">
		<meta name="msapplication-TileImage" content="<?php echo $domain; ?>mstile-144x144.png">
		
		<!-- Google+ -->
		<link rel="publisher" href="https://plus.google.com/107397414095793132493">
		
		<!-- Open Graph -->
		<meta property="og:image" content="<?php echo $domain; ?>img/pka.png">
		<meta property="og:site_name" content="Painkiller Already Archive">
		
		<!-- Twitter -->
		<meta property="twitter:site" content="@PKA_Archive">
		<meta property="twitter:creator" content="@nehalvpatel">
		<meta property="twitter:domain" content="www.painkilleralready.info">
		
		<!-- CSS -->
		<link rel="stylesheet" type="text/css" href="<?php echo $domain; ?>css/main.css?ver=<?php echo $commit_count; ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo $domain; ?>css/person.css?ver=<?php echo $commit_count; ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo $domain; ?>css/fontawesome.css?ver=<?php echo $commit_count; ?>">
		
		<!-- IE8 -->
		<!--[if lt IE 9]>
			<link rel="stylesheet" href="<?php echo $domain; ?>css/fontawesome-ie7.css">
			<script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7/html5shiv.js"></script>
			<script src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.js"></script>
		<![endif]-->
	</head>
	<body itemscope itemtype="http://schema.org/WebPage">
		<aside class="sidebar">
			<nav id="sidebar">
				<div class="search-form"><input class="search-field" type="search" id="search-field" name="search" placeholder="Search" results="0"></div>
				<h3 id="episodes_title">Episodes</h3>
				<ul>
<?php
	foreach ($Podcast->getEpisodes() as $episode) {
?>
					<li>
						<a href="<?php echo $domain; ?>episode/<?php echo $episode["Number"]; ?>">#<?php echo $episode["Number"]; ?></a>
					</li>
<?php
	}
?>
				</ul>
			</nav>
		</aside>
		<section class="main">
			<header>
				<a href="#" class="toggle-menu icon-reorder"></a>
				<h1>Painkiller Already Archive</h1>
			</header>
			<div id="container">
				<div id="image">
					<img src="img/people/<?php echo $Person->getID(); ?>a.png" />
				</div>
				<div id="details">
					<div id="overview">
						<h2><?php echo $Person->getName(); ?></h2>
						<p><?php echo $Person->getOverview(); ?></p>
					</div>
					<div id="social-icons">
<?php
	if($Person->getFacebook() != ""){
?>
						<a href="http://www.facebook.com/<?php echo $Person->getFacebook(); ?>"><img src="img/facebook.png" /></a>
<?php
	}
	if($Person->getTwitter() != ""){
?>
						<a href="http://www.twitter.com/<?php echo $Person->getTwitter(); ?>"><img src="img/twitter.png" /></a>
<?php
	}
	if($Person->getTwitch() != ""){
?>
						<a href="http://www.twitch.tv/<?php echo $Person->getTwitch(); ?>"><img src="img/twitch.png" /></a>
<?php
	}
	if($Person->getReddit() != ""){
?>
						<a href="http://www.reddit.com/user/<?php echo $Person->getReddit(); ?>"><img src="img/reddit.png" /></a>
<?php
	}
?>
					</div>
				</div>
			</div>
		</section>
		<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script type="text/javascript">var domain = "<?php echo $domain; ?>";</script>
		<script type="text/javascript" src="<?php echo $domain; ?>js/main.js?ver=<?php echo $commit_count; ?>"></script>
		<!--[if lt IE 9]>
			<script type="text/javascript">
				$(document).ready(function(){
					$(".toggle-menu").click(function(){
						$(".main").css({"display": "none"});
						$(".main").css({"display": "block"});
					});
				});
			</script>
		<![endif]-->
	</body>
</html>