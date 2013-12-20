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
	
	$guests = $current_episode->getGuests();
	if ($guests == "") {
		$guests = "Nobody";
	} elseif (strpos($guests, ",") !== FALSE) {
		$guests_explosion = explode(",", $guests);
		
		if (count($guests_explosion) > 2) {
			$guests_explosion[count($guests_explosion) - 1] = "and " . $guests_explosion[count($guests_explosion) - 1];
			$guests = join(", ", $guests_explosion);
		} else {
			$guests = join(" and ", $guests_explosion);
		}
	}
	
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Meta -->
		<meta charset="utf-8">
		<meta name="description" content="Three gamers discuss games, current events, and tell a few stories.">
		<base href="<?php echo $domain; ?>">
		<link rel="canonical" href="<?php echo $canonical; ?>">
		<title><?php if($source == "get") { echo "Episode #" . $current_episode->getNumber() . " &middot; "; } ?>Painkiller Already Archive</title>
		
		<!-- Open Graph -->
		<meta property="og:image" content="<?php echo $domain; ?>img/pka.png">
		<meta property="og:site_name" content="Painkiller Already Archive">
		
<?php
		if ($source == "get") {
?>
		<meta property="og:type" content="music.song">
		<meta property="og:title" content="Painkiller Already #<?php echo $current_episode->getNumber(); ?>">
		<meta property="og:description" content="Guests: <?php echo $guests; ?>">
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
		<meta property="og:description" content="Three gamers discuss games, current events, and tell a few stories.">
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
			<div id="links">
				<a href="http://www.reddit.com/r/PKA/">
				<div id="reddit">
				</div>
				</a>
				<a href="http://www.youtube.com/WoodysGamertag">
				<div id="woody">
				</div>
				</a>
				<a href="http://www.youtube.com/WingsofRedemption">
				<div id="wings">
				</div>
				</a>
				<a href="http://www.youtube.com/LeftyOX">
				<div id="lefty">
				</div>
				</a>
				<div class="clear"></div>
			</div>
			<h1>Painkiller Already Archive</h1>
			<div class="clear"></div>
		</div>
		<div id="episodes">
<?php
	foreach ($Podcast->getEpisodes() as $episode) {
?>
			<a href="episode/<?php echo $episode["Number"]; ?>">
				<div class="episode<?php echo ($episode['Number'] == $_GET['episode']) ? ' active' : null; ?>">
					<h3>Painkiller Already #<?php echo $episode["Number"]; ?></h3>
				</div>
			</a>
<?php
	}
?>
		</div>
		<div id="controller">
			<div id="title">
				<h2><?php echo "Painkiller Already #" . $current_episode->getNumber(); ?></h2>
				<?php if ($current_episode->getReddit() != "") { ?><a href="http://www.reddit.com/comments/<?php echo $current_episode->getReddit(); ?>"><div id="discussion">Discussion</div></a><?php } ?>
			</div>
			<div id="video-player">
				<iframe height="315" src="//www.youtube.com/embed/<?php echo $current_episode->getYouTube(); ?>" frameborder="0" allowfullscreen></iframe>
			</div>
			<div id="timeline">
				<div id="line">
					
<?php
	/*	This is a complicated code. In here we are trying to create a new array based off the old array of the timeline values.
	*	We want the new array to be a multi-dimensional array. Each element contains the timeline timestamp (time in seconds), the value (timeline label) and the timestamp of the next topic.
	*	This is so we can find the time of the beginning and the end of each topic and will help create the graphical timeline.
	*/
	$timestamps = $current_episode->getTimestamps();
	if(empty($timestamps)){
?>
					<p>No timeline available</p>
<?php
	} else {
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
	}
?>

				</div>
			</div>
		</div>
	</body>
</html>