<?php

	require_once("config.php");

	if (isset($_SERVER["HTTP_USER_AGENT"]) && (strpos($_SERVER["HTTP_USER_AGENT"], "MSIE") !== false)) header("X-UA-Compatible: IE=edge,chrome=1");
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Meta -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="canonical" href="<?php echo $domain; ?>credits">
		<title>Developers and Contributors &middot; <?php echo $Podcast->getTitle(); ?></title>
		
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
		
		<!-- CSS -->
		<link rel="stylesheet" type="text/css" href="<?php echo $domain; ?>css/main.css?ver=<?php echo $commit_count; ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo $domain; ?>css/fontawesome.css?ver=<?php echo $commit_count; ?>">
		
		<!-- IE8 -->
		<!--[if lt IE 9]>
			<link rel="stylesheet" href="<?php echo $domain; ?>css/fontawesome-ie7.css">
			<script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7/html5shiv.js"></script>
			<script src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.js"></script>
		<![endif]-->
	</head>
	<body>
<?php include_once("templates/header.php"); ?>
				<div id="page-title">
					<h2>Developers and Contributors</h2>
					<p>Thank you to everyone for making this website such a huge success. This website would not have become what it is without the immense effort our developers and contributors donate.</p>
				</div>
				<div class="section">
					<h3>Developers</h3>
					<ul>
						<li>Nehal Patel, for his amazing website development and engineering skills, knowledge and abilities.</li>
						<li>PoweredMinecart, for his great work in user interface, design and timelines.</li>
					</ul>
				</div>
				<div class="section">
					<h3>Contributors</h3>
					<ul>
						<li>ThePKAHistorian, for his superior work ethic and timelining work.</li>
						<li>IrishDelta, for his superior work ethic and timelining work.</li>
						<li>gotmilk55, for his timelining work.</li>
						<li>Shizzable, for his help in data entry and timelining work.</li>
						<li>BandWagonEffect, for his timelining work.</li>
						<li>Spacemoses, for his timelining work.</li>
						<li>Minimee321, for his timelining work.</li>
						<li>mynewemporer, for his timelining work.</li>
					</ul>
				</div>
<?php include_once("templates/footer.php"); ?>
	</body>
</html>