<?php

	require_once("config.php");
	require_once("class.person.php");
	
	if (isset($_GET["person"])) {
		try {
			$Person = new Person($con);
			$Person->initWithID($_GET["person"]);
		} catch (Exception $e) {
			header("Location: " . $domain . "404.php");
		}
	} else {
		header("Location: " . $domain . "404.php");
	}
	
	$host_count = 0;
	$guest_count = 0;
	$sponsor_count = 0;
	$highlighted_episodes = array();
	foreach ($Podcast->getEpisodes() as $num=>$episode) {
		$highlighted_episodes[$num] = false;
		if (in_array($Person->getID(), json_decode($episode["Hosts"], true))) {
			$highlighted_episodes[$num] = true;
			$host_count++;
		}
		
		if (in_array($Person->getID(), json_decode($episode["Guests"], true))) {
			$highlighted_episodes[$num] = true;
			$guest_count++;
		}
		
		if (in_array($Person->getID(), json_decode($episode["Sponsors"], true))) {
			$highlighted_episodes[$num] = true;
			$sponsor_count++;
		}
	}
	if (isset($_SERVER["HTTP_USER_AGENT"]) && (strpos($_SERVER["HTTP_USER_AGENT"], "MSIE") !== false)) header("X-UA-Compatible: IE=edge,chrome=1");
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Meta -->
		<meta charset="utf-8">
		<meta name="apple-mobile-web-app-title" content="<?php echo $Podcast->getTitle(); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<meta name="description" content="<?php echo $Person->getOverview(); ?>">
		<link rel="canonical" href="<?php echo $domain . "person/" . $Person->getID(); ?>">
		<title><?php echo $Person->getName(); ?> · <?php echo $Podcast->getTitle(); ?></title>
		
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
		<meta property="og:image" content="<?php echo $domain; ?>img/people/<?php echo $Person->getID(); ?>a.png">
		<meta property="og:site_name" content="<?php echo $Podcast->getTitle(); ?>">
		<meta property="og:type" content="profile">
		<meta property="og:title" content="<?php echo $Person->getName(); ?>">
		<meta property="og:description" content="<?php echo $Person->getOverview(); ?>">
		<meta property="og:url" content="<?php echo $domain . "person/" . $Person->getID(); ?>">
		<meta property="fb:profile_id" content="<?php echo $Person->getFacebook(); ?>">
		<meta property="profile:first_name" content="<?php echo $Person->getFirstName(); ?>">
		<meta property="profile:last_name" content="<?php echo $Person->getLastName(); ?>">
		<meta property="profile:username" content="<?php echo $Person->getName(); ?>">
		<meta property="profile:gender" content="<?php echo ($Person->getGender() != "1" ? "female" : "male") ?>">
		
		<!-- CSS -->
		<link rel="stylesheet" type="text/css" href="<?php echo $domain; ?>css/main.css?ver=<?php echo filemtime("css/main.css"); ?>">
		<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/3.0.2/css/font-awesome.min.css">
	</head>
	<body>
<?php include_once("templates/header.php"); ?>
				<div id="image">
					<img id="person-image" class="person-image" alt="<?php echo $Person->getName(); ?>" title="<?php echo $Person->getName(); ?>" src="<?php echo $domain; ?>img/people/<?php echo $Person->getID(); ?>a.png" />
				</div>
				<div id="details">
					<div id="overview" class="section">
						<h2 class="section-header"><?php echo $Person->getName(); ?></h2>
						<p><?php echo $Person->getOverview(); ?></p>
					</div>
<?php
	if (count($Person->getSocialLinks()) > 0) {
?>
					<div id="social-icons" class="section items">
						<h2 class="section-header">Social</h2>
<?php
		foreach ($Person->getSocialLinks() as $social_link) {
?><a class="item" href="<?php echo $social_link["Link"]; ?>"><img alt="<?php echo $social_link["Name"]; ?>" title="<?php echo $social_link["Name"]; ?>" src="<?php echo $domain; ?>img/<?php echo $social_link["Image"]; ?>"></a><?php
		}
?>
					</div>
<?php
	}
?>
					<div id="stats" class="section">
						<h2 class="section-header">Stats</h2>
						<p><?php echo ($Person->getGender() != "1" ? "She" : "He") ?> has hosted <strong><?php echo $host_count; ?></strong> episode<?php echo ($host_count != 1 ? "s" : "") ?>, been a guest on <strong><?php echo $guest_count; ?></strong> episode<?php echo ($guest_count != 1 ? "s" : "") ?>, and sponsored <strong><?php echo $sponsor_count; ?></strong> episode<?php echo ($sponsor_count != 1 ? "s" : "") ?>.</p>
					</div>
				</div>
				<div class="clear"></div>
<?php
	$recent_videos = $Person->getRecentYouTubeVideos();
	if (count($recent_videos) > 0) {
?>
				<div id="youtube-videos" class="section">
					<h2 class="section-header">Recent YouTube Videos</h2>
<?php
		foreach ($recent_videos as $video) {
?>
					<a href="<?php echo htmlspecialchars($video["Link"]); ?>" class="video">
						<img alt="<?php echo $video["Title"]; ?>" title="<?php echo $video["Title"]; ?>" class="video-thumbnail" src="<?php echo $video["Thumbnail"]; ?>">
						<span class="video-title"><?php echo $video["Title"]; ?></span>
						<div class="video-details">
							<span class="video-timestamp"><i class="icon-time"></i> <?php echo $video["Duration"]; ?></span>
							<span class="video-comments"><i class="icon-comments"></i> <?php echo number_format($video["Comments"]); ?> Comment<?php echo ($video["Comments"] != 1 ? "s" : "") ?></span>
						</div>
					</a>
<?php
		}
?>
					<div class="clear"></div>
				</div>
<?php } include_once("templates/footer.php"); ?>
	</body>
</html>