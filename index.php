<?php

	require_once("config.php");
	
	if (isset($_GET["episode"]) && is_numeric($_GET["episode"])) {
		try {
			$current_episode = new Episode($con);
			$current_episode->initWithNumber($Podcast->trimEpisodeNumber($_GET["episode"]));
			$canonical = $domain . "episode/" . $current_episode->getNumber();
			$source = "get";
		} catch (Exception $e) {
			$current_episode = new Episode($con);
			$current_episode->initWithIdentifier($Podcast->getLatestEpisode());
			$canonical = $domain;
			$source = "latest";
		}
	} else {
		$current_episode = new Episode($con);
		$current_episode->initWithIdentifier($Podcast->getLatestEpisode());
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
	
	if (isset($_SERVER["HTTP_USER_AGENT"]) && (strpos($_SERVER["HTTP_USER_AGENT"], "MSIE") !== false)) header("X-UA-Compatible: IE=edge,chrome=1");
	
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
		<link rel="search" type="application/opensearchdescription+xml" href="<?php echo $domain; ?>opensearchdescription.xml" title="Painkiller Already Archive">
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
		<meta property="twitter:site" content="@PKA_Archive">
		<meta property="twitter:creator" content="@nehalvpatel">
		<meta property="twitter:domain" content="www.painkilleralready.info">
<?php

		if ($source == "get") {
?>
		<meta property="twitter:card" content="player">
		<meta property="twitter:title" content="Painkiller Already #<?php echo $current_episode->getNumber(); ?>">
		<meta property="twitter:description" content="Guests: <?php echo $guests_list; ?>">
		<meta property="twitter:image:src" content="http://i1.ytimg.com/vi/<?php echo $current_episode->getYouTube(); ?>/hqdefault.jpg">
		<meta property="twitter:player" content="https://www.youtube.com/embed/<?php echo $current_episode->getYouTube(); ?>">
		<meta property="twitter:player:height" content="1280">
		<meta property="twitter:player:width" content="720">
<?php
		} else {
?>
		<meta property="twitter:card" content="summary">
		<meta property="twitter:title" content="<?php echo $Podcast->getName(); ?>">
		<meta property="twitter:description" content="<?php echo $Podcast->getDescription(); ?>">
		<meta property="twitter:image:src" content="<?php echo $domain; ?>img/pka.png">
<?php
		}
		
?>
		
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
		<aside class="sidebar">
			<nav id="sidebar">
				<div class="search-form"><input class="search-field" type="search" id="search-field" name="search" placeholder="Search"></div>
				<h3>Episodes</h3>
				<ul data-current="<?php echo $current_episode->getIdentifier(); ?>">
<?php
	foreach ($Podcast->getEpisodes() as $episode) {
?>
					<li data-episode="<?php echo $episode["Identifier"]; ?>"<?php if ((isset($_GET["episode"])) && ($episode["Number"] == $_GET["episode"])) { echo ' id="active"'; } ?>>
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
				<h2>Painkiller Already #<?php echo $current_episode->getNumber(); ?></h2>
				<div class="info">
					<span class="published" title="Date Published"><i class="icon-time"></i><small><time datetime="<?php echo $current_episode->getDate(); ?>"><?php echo date("F d, Y", strtotime($current_episode->getDate())); ?></time></small></span>
					<?php if ($current_episode->getReddit() != "") { ?><a class="comments" title="Discussion Comments" href="http://www.reddit.com/comments/<?php echo $current_episode->getReddit(); ?>"><i class="icon-comments"></i><small id="comments" data-reddit="<?php echo $current_episode->getReddit(); ?>">Comments</small></a><?php echo PHP_EOL; } ?>
					<?php if ($current_episode->getTimelineAuthor() != "") { ?><a class="author" title="Timeline Author" href="<?php echo $current_episode->getTimelineAuthorLink(); ?>"><i class="icon-user"></i><small><?php echo $current_episode->getTimelineAuthor(); ?></small></a><?php } ?>
				</div>
				<div id="rock-hardplace"></div>
				<div id="video">
					<iframe src="//www.youtube.com/embed/<?php echo $current_episode->getYouTube(); ?>?enablejsapi=1&start=<?php if (isset($_GET["timestamp"])) { echo $_GET["timestamp"]; } ?>" frameborder="0" allowfullscreen id="player"></iframe>
				</div>
				<div id="hosts" class="section people">
					<h4>Hosts</h4>
<?php

	foreach ($hosts as $host) {
?><a target="_blank" href="<?php echo $domain . "person/" . $host->getID(); ?>" title="<?php echo $host->getName(); ?>"><div class="person"><img alt="<?php echo $host->getName(); ?>" src="<?php echo $domain . "img/people/" . $host->getID(); ?>.png"></div></a><?php
	}
	
?>
				</div>
<?php
	
	if (count($guests) > 0) {
?>
				<div id="guests" class="section people">
					<h4>Guests</h4>
<?php

	foreach ($guests as $guest) {
?><a target="_blank" href="<?php echo $domain . "person/" . $guest->getID(); ?>" title="<?php echo $guest->getName(); ?>"><div class="person"><img alt="<?php echo $guest->getName(); ?>" src="<?php echo $domain . "img/people/" . $guest->getID(); ?>.png"></div></a><?php		
	}

?>
				</div>
<?php
	}
	
	if (count($sponsors) > 0) {
?>
				<div id="sponsors" class="section people">
					<h4>Sponsors</h4>
<?php

		foreach ($sponsors as $sponsor) {
?><a target="_blank" href="<?php echo $domain . "person/" . $sponsor->getID(); ?>" title="<?php echo $sponsor->getName(); ?>"><div class="person"><img alt="<?php echo $sponsor->getName(); ?>" src="<?php echo $domain . "img/people/" . $sponsor->getID(); ?>.png"></div></a><?php		
		}

?>
				</div>
<?php
		}
?>
				<div id="timeline-clear"></div>
<?php
		
		/*		This is a complicated code. In here we are trying to create a new array based off the old array of the timeline values.
		*		We want the new array to be a multi-dimensional array. Each element contains the timeline timestamp (time in seconds), the value (timeline label) and the timestamp of the next topic.
		*		This is so we can find the time of the beginning & the end of each topic and will help create the graphical timeline.
		*/
		$timestamps = $current_episode->getTimestamps();
		if (count($timestamps) > 0) {
?>
				<div id="timeline-horizontal" class="section">
					<h4>Timeline</h4>
					<div class="timeline">
<?php

			foreach ($timestamps as $timeline_key => $timeline_element) {
?>
						<a class="timelink" href="<?php echo $domain . "episode/" . $current_episode->getNumber() . "?timestamp=" . $timeline_element["Begin"]; ?>" data-begin="<?php echo $timeline_element["Begin"]; ?>" data-end="<?php echo $timeline_element["End"]; ?>">
							<div class="topic" style="width: <?php echo $timeline_element["Width"]; ?>%">
								<div class="tooltip<?php echo ($timeline_element["Begin"] > ($current_episode->getYouTubeLength()) / 2) ? " right" : null; ?>" id="<?php echo $timeline_key; ?>">
									<div class="triangle"></div>
									<span><?php echo $timeline_element["Value"]; ?></span>
								</div>
							</div>
						</a>
<?php
			}
?>
					</div>
				</div>
				<div id="timeline-vertical" class="section">
					<table id="timeline-table">
						<thead>
							<tr>
								<th>Time</th>
								<th>Event</th>
							</tr>
						</thead>
						<tbody>
<?php
		
			foreach ($timestamps as $timestamp) {
?>
							<tr>
								<td class="timestamp"><a class="timelink" href="<?php echo $domain . "episode/" . $current_episode->getNumber() . "?timestamp=" . $timestamp["Begin"]; ?>" data-begin="<?php echo $timestamp["Begin"]; ?>" data-end="<?php echo $timestamp["End"]; ?>"><?php echo $timestamp["HMS"]; ?></a></td>
								<td class="event"><?php echo $timestamp["Value"]; ?><?php if ($timestamp["URL"] != "") { ?><a target="_blank" href="<?php echo $timestamp["URL"]; ?>"><i class="icon-external-link"></i></a><?php } ?></td>
							</tr>
<?php
			}
		
?>
						</tbody>
					</table>
<?php
		}
?>
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