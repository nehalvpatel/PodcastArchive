<?php

	require_once("config.php");
	
	$keys = array_keys($Podcast->getEpisodes());
	
	$current_episode = (isset($_GET['episode'])) ? new Episode('PKA_'.$_GET['episode'], $con) : new Episode($keys[0], $con);
	
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Painkiller Already Achive</title>
		<link rel="stylesheet" type="text/css" href="css/main.css" />
		<script type="text/javascript" src="js/scroll.js"></script>
	</head>
	<body>
		<div id="episodes">
<?php
	foreach($Podcast->getEpisodes() as $episode_name=>$episode){
?>
			<a href="index.php?episode=<?php echo substr($episode_name, 4, 3); ?>">
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
				<h1><?php echo $current_episode->getTitle(); ?> - Painkiller Already Archive</h1>
			</div>
			<div id="videos">
				<div class="video-player">
					<iframe width="560" height="315" src="//www.youtube.com/embed/<?php echo $current_episode->getYouTube(); ?>" frameborder="0" allowfullscreen></iframe>
				</div>
				<div class="video-player">
					<iframe width="560" height="315" src="//www.youtube.com/embed/9I55AjbUjis" frameborder="0" allowfullscreen></iframe>
				</div>
				<div class="clear"></div>
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
		
		// Functionality to toggle topic background colours.
		if($toggler){
?>
					<div class="topic black" style="width: <?php echo $timeline_element_percentage; ?>%">
					
					</div>
<?php
		} else {
?>
					<div class="topic grey" style="width: <?php echo $timeline_element_percentage; ?>%">
					
					</div>
<?php
		}
		$toggler = !$toggler;
	}
?>
				</div>
				<p>
<?php
/*
	foreach ($current_episode->getTimestamps() as $timestamp) {
		$init = $timestamp["Timestamp"];
		$hours = floor($init / 3600);
		$minutes = floor(($init / 60) % 60);
		$seconds = $init % 60;
		
		printf("%02d:%02d:%02d", $hours, $minutes, $seconds);
		
		echo " - " . $timestamp["Value"];
*/?>
<br>
<?php
	/*}*/
?>
				</p>
			</div>
		</div>
		<script type="text/javascript">
		function adjustHeight(){
			var newHeight = window.innerHeight-100;
			$('#height-obj').height(newHeight);
		}
		$(document).ready(function(){

        $('#episodes').tinyscrollbar();

		});
		</script>
	</body>
</html>