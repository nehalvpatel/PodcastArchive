<?php

	require_once("config.php");
	
	$settings = $Podcast->getSettings();
	$domain = $settings["Domain"];
	
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
		<meta name="description" content="<?php if ($source == "latest") { echo "Four gamers discuss games, current events, and tell a few stories."; } else { echo "Guests: " . $guests_list; } ?>">
		<base href="<?php echo $domain; ?>">
		<link rel="canonical" href="<?php echo $canonical; ?>">
		<link rel="alternate" type="application/rss+xml" title="<?php echo $Podcast->getName(); ?>" href="http://feeds.feedburner.com/<?php echo $Podcast->getFeedburner(); ?>">
		<title><?php if($source == "get") { echo "Episode #" . $current_episode->getNumber() . " &middot; "; } ?>Painkiller Already Archive</title>
		
		<!-- Icons -->
		<link rel="apple-touch-icon" sizes="57x57" href="apple-touch-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="114x114" href="apple-touch-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="72x72" href="apple-touch-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="144x144" href="apple-touch-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="60x60" href="apple-touch-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="120x120" href="apple-touch-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="76x76" href="apple-touch-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="152x152" href="apple-touch-icon-152x152.png">
		<link rel="icon" type="image/png" href="favicon-196x196.png" sizes="196x196">
		<link rel="icon" type="image/png" href="favicon-160x160.png" sizes="160x160">
		<link rel="icon" type="image/png" href="favicon-96x96.png" sizes="96x96">
		<link rel="icon" type="image/png" href="favicon-32x32.png" sizes="32x32">
		<link rel="icon" type="image/png" href="favicon-16x16.png" sizes="16x16">
		<meta name="msapplication-TileColor" content="#ffffff">
		<meta name="msapplication-TileImage" content="mstile-144x144.png">
		
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
		<meta property="og:description" content="Four gamers discuss games, current events, and tell a few stories.">
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

		<!-- CSS -->
		<link rel="stylesheet" type="text/css" href="<?php echo $domain; ?>css/main.css" />
		
		<!-- JS -->
		<script type="text/javascript">
			function disappear(id){
				document.getElementById(id).style.display = 'none';
			}
			function appear(id){
				document.getElementById(id).style.display = 'block';
			}
		</script>
	</head>
	<body>
		<div id="header">
			<h1>Painkiller Already Archive</h1>
			<div class="clear"></div>
		</div>
		<div id="episodes">
<?php
	foreach ($Podcast->getEpisodes() as $episode) {
?>
			<a href="episode/<?php echo $episode["Number"]; ?>">
				<div class="episode<?php echo ($episode['Number'] == $_GET['episode']) ? ' active' : null; ?>">
					<h3 style="<?php echo (floor($episode["Number"]) != $episode["Number"]) ? 'margin-left: -10px;' : null; ?>">Episode #<?php echo $episode["Number"]; ?></h3>
					<?php if ($episode["Reddit"] != "") { ?><img class="discussion" onclick="window.location.href='http://www.reddit.com/comments/<?php echo $episode["Reddit"]; ?>'; return false;" src="<?php echo $domain; ?>img/discussion.png"><?php } ?>
				</div>
			</a>
<?php
	}
?>
		</div>
		<div id="controller">
			<iframe src="//www.youtube.com/embed/<?php echo $current_episode->getYouTube(); ?>" frameborder="0" allowfullscreen></iframe>
			<div id="hosts">
				<h3>Hosts</h3>
<?php

	foreach ($hosts as $host) {
?>
				<a href="<?php echo $host->getURL(); ?>">
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
				<h3>Guests</h3>
<?php

	foreach ($guests as $guest) {
?>
				<a href="<?php echo $guest->getURL(); ?>">
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
				<h3>Sponsors</h3>
<?php

	foreach ($sponsors as $sponsor) {
?>
				<a href="<?php echo $sponsor->getURL(); ?>">
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
	
	/*	This is a complicated code. In here we are trying to create a new array based off the old array of the timeline values.
	*	We want the new array to be a multi-dimensional array. Each element contains the timeline timestamp (time in seconds), the value (timeline label) and the timestamp of the next topic.
	*	This is so we can find the time of the beginning & the end of each topic and will help create the graphical timeline.
	*/
	$timestamps = $current_episode->getTimestamps();
	if (empty($timestamps)) {
?>
			<div id="timeline" style="padding: 10px;">
				<h3>Timeline</h3>
				<p class="no-timeline">No timeline available</p>
<?php
	} else {
?>
			<div id="timeline">
				<h3>Timeline</h3>
				<div id="line">
<?php
		$timeline_array = array();
		$i = 0;
		foreach ($timestamps as $timestamp){
			$timeline_array[] = array($timestamp['Timestamp'], $timestamp['Value']);
			// Set the previous array element's finishing time to the currents starting time.
			if(isset($timeline_array[count($timeline_array)-2])){
				$timeline_array[count($timeline_array)-2][2] = $timestamp['Timestamp'];
			}
			$last_timestamp = $timestamp['Timestamp'];
		}
		// The last topic ends when the episode ends.
		$timeline_array[count($timeline_array)-1][2] = $current_episode->getLength();
		
		// We now start printing the timeline.
		$toggler = true;
		foreach($timeline_array as $timeline_element){
			// Find size of timeline element .
			$timeline_element_size = $timeline_element[2]-$timeline_element[0];
			
			// Express the timeline size as a quotent of the full current episode size.
			$timeline_element_quotent = $timeline_element_size/$current_episode->getLength();
			
			// Multiply by 100 to express in percentage form.
			$timeline_element_percentage = $timeline_element_quotent*100;
?>
					<div id="topic" style="width:<?php echo $timeline_element_percentage; ?>%" onmouseover="appear('<?php echo $i; ?>');" onmouseout="disappear('<?php echo $i; ?>');">
						<div class="tooltip<?php echo($timeline_element[0] > ($current_episode->getLength())/2) ? ' right' : null; ?>" id="<?php echo $i; ?>" >
							<div class="triangle">
							
							</div>
							<span><?php echo $timeline_element[1]; ?></span>
						</div>
					</div>
<?php
			$i++;
		}
?>
				</div>
<?php
	}
?>

				</div>
			</div>
		</div>
	</body>
</html>