<?php

	require_once("config.php");
	$episodes = array_reverse($Podcast->getEpisodes());
	
	$keys = array_keys($episodes);
	$latest_episode = new Episode($keys[0], $con);
	
?>
<!DOCTYPE html>
<html lang="en" id="html">
	<head>
		<title>Painkiller Already Achive</title>
		<link rel="stylesheet" type="text/css" href="css/main.css" />
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
		<script type="text/javascript" src="js/scroll.js"></script>
	</head>
	<body onresize="adjustHeight();" onload="adjustHeight();">
		<div id="header">
			<h1>Painkiller Already Archive</h1>
		</div>
		<div id="episodes">
			<div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
			<div class="viewport" id="height-obj">
				<div class="overview">
<?php
	foreach($episodes as $episode){
?>
					<div class="episode">
						<div class="title">
							<h3><?php echo $episode['Title']; ?></h3>
						</div>
						<div class="video">
						</div>
					</div>
<?php
	}
?>					
				</div>
			</div>
		</div>
		<div id="controller">
			<div id="title">
				<h1><?php echo $latest_episode->getTitle(); ?></h1>
			</div>
			<div id="videos">
				<div class="video-player">
					<iframe width="560" height="315" src="//www.youtube.com/embed/<?php echo $latest_episode->getYouTube(); ?>" frameborder="0" allowfullscreen></iframe>
				</div>
				<div class="video-player">
					<iframe width="560" height="315" src="//www.youtube.com/embed/9I55AjbUjis" frameborder="0" allowfullscreen></iframe>
				</div>
			</div>
			<div id="timeline">
				<h3>Timeline</h3>
				<p>
<?php
	foreach ($latest_episode->getTimestamps() as $timestamp) {
		$init = $timestamp["Timestamp"];
		$hours = floor($init / 3600);
		$minutes = floor(($init / 60) % 60);
		$seconds = $init % 60;
		
		printf("%02d:%02d:%02d", $hours, $minutes, $seconds);
		
		echo " - " . $timestamp["Value"];
?>
<br>
<?php
	}
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