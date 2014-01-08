<?php

	require_once("config.php");
	
	$domain = "http://" . $_SERVER["HTTP_HOST"] . str_replace(basename($_SERVER["PHP_SELF"]), "", $_SERVER["PHP_SELF"]);
	
	if (isset($_GET["episode"]) && is_numeric($_GET["episode"])) {
		try {
			$current_episode = new Episode("PKA_" . $Podcast->padEpisodeNumber($_GET["episode"]), $con);
			$canonical = $domain . "episode/" . $current_episode->getNumber();
			$source = "get";
		} catch (Exception $e) {
			$current_episode = new Episode($Podcast->getLatestEpisode(), $con);
			$canonical = $domain;
			$source = "latest";
		}
	} else {
		$current_episode = new Episode($Podcast->getLatestEpisode(), $con);
		$canonical = $domain;
		$source = "latest";
	}
	
	$hosts = array();
	$hosts_list = array();
	foreach (json_decode($current_episode->getHosts(), true) as $host) {
		$host_profile = new Person($con);
		$host_profile->initWithID($host);
		
		$hosts[] = $host_profile;
		$hosts_list[] = $host_profile->getName();
	}
	$hosts_list = join(", ", $hosts_list);
	
	$guests = array();
	$guests_list = array();
	foreach (json_decode($current_episode->getGuests(), true) as $guest) {
		$guest_profile = new Person($con);
		$guest_profile->initWithID($guest);
		
		$guests[] = $guest_profile;
		$guests_list[] = $guest_profile->getName();
	}
	
	if (count($guests_list) == 0) {
		$guests_list = "Nobody";
	} else {
		if (count($guests_list) > 2) {
			$guests_list[count($guests_list) - 1] = "and " . $guests_list[count($guests_list) - 1];
			$guests_list = join(", ", $guests_list);
		} else {
			$guests_list = join(" and ", $guests_list);
		}
	}
	
	$sponsors = array();
	foreach (json_decode($current_episode->getSponsors(), true) as $sponsor) {
		$sponsor_profile = new Person($con);
		$sponsor_profile->initWithID($sponsor);
		
		$sponsors[] = $sponsor_profile;
	}
	
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Meta -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<meta name="description" content="<?php if ($source == "latest") { echo $Podcast->getDescription(); } else { echo "Guests: " . $guests_list; } ?>">
		<link rel="canonical" href="<?php echo $canonical; ?>">
		<link rel="alternate" type="application/rss+xml" title="<?php echo $Podcast->getName(); ?>" href="http://feeds.feedburner.com/<?php echo $Podcast->getFeedburner(); ?>">
		<title><?php if($source == "get") { echo "Episode #" . $current_episode->getNumber() . " &middot; "; } ?>Painkiller Already Archive</title>
		
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
		
<?php
		if ($source == "get") {
?>
		<meta property="og:type" content="music.song">
		<meta property="og:title" content="Painkiller Already #<?php echo $current_episode->getNumber(); ?>">
		<meta property="og:description" content="Guests: <?php echo $guests_list; ?>">
		<meta property="og:url" content="<?php echo $domain; ?>episode/<?php echo $current_episode->getNumber(); ?>">
		<meta property="og:audio" content="http://media.blubrry.com/painkilleralready/archive.org/download/<?php echo $current_episode->getIdentifier(); ?>/<?php echo str_replace("_", "-", strtolower($current_episode->getIdentifier())); ?>.mp3">
		<meta property="og:audio:type" content="audio/vnd.facebook.bridge">
		<meta property="music:album" content="<?php echo $domain; ?>">
		<meta property="music:album:track" content="<?php echo $current_episode->getNumber(); ?>">
		<meta property="music:duration" content="<?php echo $current_episode->getLength(); ?>">
<?php
		} else {
?>
		<meta property="og:type" content="music.album">
		<meta property="og:title" content="Painkiller Already">
		<meta property="og:description" content="<?php echo $Podcast->getDescription(); ?>">
		<meta property="og:url" content="<?php echo $domain; ?>">
		<meta property="music:release_date" content="2010-04-19">
<?php
			foreach ($Podcast->getEpisodes() as $episode) {
?>
		<meta property="music:song" content="<?php echo $domain; ?>episode/<?php echo $episode["Number"]; ?>">
		<meta property="music:song:disc" content="1">
		<meta property="music:song:track" content="<?php echo $episode["Number"]; ?>"> 
<?php
			}
		}
?>
		
		<!-- Twitter -->
		<meta name="twitter:site" content="@PKA_Archive">
		<meta name="twitter:creator" content="@nehalvpatel">
		<meta name="twitter:domain" content="www.painkilleralready.info">
<?php

		if ($source == "get") {
?>
		<meta name="twitter:card" content="player">
		<meta name="twitter:title" content="Painkiller Already #<?php echo $current_episode->getNumber(); ?>">
		<meta name="twitter:description" content="Guests: <?php echo $guests_list; ?>">
		<meta name="twitter:image:src" content="http://i1.ytimg.com/vi/<?php echo $current_episode->getYouTube(); ?>/hqdefault.jpg">
		<meta name="twitter:player" content="https://www.youtube.com/embed/<?php echo $current_episode->getYouTube(); ?>">
		<meta name="twitter:player:height" content="1280">
		<meta name="twitter:player:width" content="720">
<?php
		} else {
?>
		<meta name="twitter:card" content="summary">
		<meta name="twitter:title" content="<?php echo $Podcast->getName(); ?>">
		<meta name="twitter:description" content="<?php echo $Podcast->getDescription(); ?>">
		<meta name="twitter:image:src" content="<?php echo $domain; ?>img/pka.png">
<?php
		}
		
?>
		
		<!-- CSS -->
		<link rel="stylesheet" type="text/css" href="<?php echo $domain; ?>css/main.css" />
	</head>
	<body>
		<header>
			<a href="#" class="toggle-menu fontawesome-reorder"></a>
			<h1>Painkiller Already Archive</h1>
		</header>
		<aside class="sidebar" id="sidebar">
			<h3>Episodes</h3>
			<nav>
				<ul>
<?php
	foreach ($Podcast->getEpisodes() as $episode) {
?>
					<a href="<?php echo $domain; ?>episode/<?php echo $episode["Number"]; ?>">
						<li<?php if ((isset($_GET["episode"])) && ($episode["Number"] == $_GET["episode"])) { echo ' class="active" id="active"'; } ?>>#<?php echo $episode["Number"]; ?></li>
					</a>
<?php
	}
?>
				</ul>
			</nav>
		</aside>
		<section class="main">
			<h2 id="top">Painkiller Already #<?php echo $current_episode->getNumber(); ?></h2>
			<div class="info">
				<i class="published fontawesome-time"></i><small><time datetime="<?php echo $current_episode->getDate(); ?>"><?php echo date("F d, Y", strtotime($current_episode->getDate())); ?></time></small>
				<?php if ($current_episode->getReddit() != "") { ?><a class="comments" href="http://www.reddit.com/comments/<?php echo $current_episode->getReddit(); ?>"><i class="fontawesome-comments"></i><small id="comments" data-reddit="<?php echo $current_episode->getReddit(); ?>">Comments</small></a><?php } ?>
			</div>
			<div itemprop="video" itemscope itemtype="http://schema.org/VideoObject">
				<meta itemprop="name" content="Painkiller Already #<?php echo $current_episode->getNumber(); ?>">
				<meta itemprop="description" content="Guests: <?php echo $guests_list; ?>">
				<meta itemprop="thumbnailUrl" content="http://img.youtube.com/vi/<?php echo $current_episode->getYouTube(); ?>/hqdefault.jpg">
				<meta itemprop="duration" content="<?php echo $current_episode->getDuration(); ?>">
				<meta itemprop="contentURL" content="<?php echo $current_episode->getContentURL(); ?>">
				<meta itemprop="embedURL" content="https://www.youtube.com/v/<?php echo $current_episode->getYouTube(); ?>">
				<meta itemprop="uploadDate" content="<?php echo $current_episode->getPublished(); ?>">
				<div id="player" data-youtube="<?php echo $current_episode->getYouTube(); ?>"></div>
			</div>
			<div id="hosts">
				<h4>Hosts</h4>
<?php

	foreach ($hosts as $host) {
?>
				<a target="_blank" href="<?php echo $host->getURL(); ?>">
					<div class="person">
						<img alt="<?php echo $host->getName(); ?>" src="<?php echo $domain . $host->getImage(); ?>">
					</div>
				</a>
<?php
	}
	
?>
			</div>
<?php
	
	if (count($guests) > 0) {
?>
			<div id="guests">
				<h4>Guests</h4>
<?php

	foreach ($guests as $guest) {
?>
				<a target="_blank" href="<?php echo $guest->getURL(); ?>">
					<div class="person">
						<img alt="<?php echo $guest->getName(); ?>" src="<?php echo $domain . $guest->getImage(); ?>">
						<span class="person-name"><?php echo $guest->getName(); ?></span>
					</div>
				</a>
<?php		
	}

?>
			</div>
<?php
	}
	
	if (count($sponsors) > 0) {
?>
			<div id="sponsors">
				<h4>Sponsors</h4>
<?php

		foreach ($sponsors as $sponsor) {
?>
				<a target="_blank" href="<?php echo $sponsor->getURL(); ?>">
					<div class="person">
						<img alt="<?php echo $sponsor->getName(); ?>" src="<?php echo $domain . $sponsor->getImage(); ?>">
						<span class="person-name"><?php echo $sponsor->getName(); ?></span>
					</div>
				</a>
<?php		
		}

?>
			</div>
<?php
	}
	
	$timestamps = $current_episode->getTimestamps();
	
	if (count($timestamps) > 0) {
?>
			<table id="timeline">
				<thead>
					<tr>
						<th>Time</th>
						<th>Event</th>
					</tr>
				</thead>
				<tbody>
<?php
	
		foreach ($timestamps as $timestamp) {
			$init = $timestamp["Timestamp"];
			$hours = floor($init / 3600);
			$minutes = floor(($init / 60) % 60);
			$seconds = $init % 60;
?>
					<tr>
						<td class="timestamp"><a href="javascript:void(0);" onclick="seekYT(<?php echo $init; ?>);"><?php printf("%02d:%02d:%02d", $hours, $minutes, $seconds); ?></a></td>
						<td class="event"><?php echo $timestamp["Value"]; ?><?php if ($timestamp["Type"] == "Link") { ?><a target="_blank" href="<?php echo $timestamp["URL"]; ?>"><i class="fontawesome-external-link"></i></a><?php } ?></td>
					</tr>
<?php
		}
	
?>
				</tbody>
			</table>
<?php
	}
?>
		</section>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script type="text/javascript" src="<?php echo $domain; ?>js/main.js"></script>
	</body>
</html>