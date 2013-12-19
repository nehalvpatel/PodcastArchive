<?php

	require_once("config.php");
	
	$settings = $Podcast->getSettings();
	$domain = $settings["Domain"];
	
	if (isset($_GET["episode"]) && is_numeric($_GET["episode"])) {
		$current_episode = new Episode("PKA_" . $Podcast->padEpisodeNumber($_GET["episode"]), $con);
		$canonical = $domain . "episode/" . $Podcast->trimEpisodeNumber($_GET["episode"]);
	} else {
		$current_episode = new Episode($Podcast->getLatestEpisode(), $con);
		$canonical = $domain;
	}
	
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title><?php if(!empty($_GET["episode"])) { echo $current_episode->getTitle() . " - "; } ?>Painkiller Already Archive</title>
		<link rel="stylesheet" type="text/css" href="<?php echo $domain; ?>css/main.css" />
		<link rel="canonical" href="<?php echo $canonical; ?>">
		<base href="<?php echo $domain; ?>">
	</head>
	<body>
		<div id="episodes">
<?php
	foreach ($Podcast->getEpisodes() as $episode_name => $episode) {
		$episode_explosion = explode("_", $episode_name);
		
?>
			<a href="episode/<?php echo $Podcast->trimEpisodeNumber($episode_explosion[1]); ?>">
				<div class="episode">
					<h3><?php echo $episode['Title']; ?></h3>
				</div>
			</a>
<?php
	}
?>
		</div>
		<div id="controller">
			<div id="title">
				<h1><?php echo $current_episode->getTitle(); ?></h1>
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
	$timeline_array = array();
	foreach ($current_episode->getTimestamps() as $timestamp){
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
		// Find size of timeline element.
		$timeline_element_size = $timeline_element[2]-$timeline_element[0];
		
		// Express the timeline size as a quotent of the full current episode size.
		$timeline_element_quotent = $timeline_element_size/$current_episode->getLength();
		
		// Multiply by 100 to express in percentage form.
		$timeline_element_percentage = $timeline_element_quotent*100;
?>
					<div class="topic" style="width: <?php echo $timeline_element_percentage; ?>%">
					
					</div>
<?php
	}
?>
				</div>
			</div>
		</div>
	</body>
</html>