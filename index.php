<?php
	require_once("config.php");
	
	if ((isset($_GET["episode"])) && (is_numeric($_GET["episode"]))) {
		try {
			$current_episode = new Episode($con);
			$current_episode->initWithNumber($Podcast->trimEpisodeNumber($_GET["episode"]));
			$canonical = $domain . "episode/" . $current_episode->getNumber();
			$source = "get";
		} catch (Exception $e) {
			$current_episode = new Episode($con);
			$current_episode->initWithIdentifier($Podcast->getLatestEpisode()["Identifier"]);
			$canonical = $domain;
			$source = "latest";
		}
	} elseif ((isset($_GET["episode"])) && ($_GET["episode"] == "random")) {
		header("Location: " . $domain . "episode/" . $Podcast->getRandomEpisode()["Number"]);
	} else {
		$current_episode = new Episode($con);
		$current_episode->initWithIdentifier($Podcast->getLatestEpisode()["Identifier"]);
		$canonical = $domain;
		$source = "latest";
	}
	
	// form handling
	if(!empty($_POST)){
		$errors = array();
		
		if(isset($_SESSION["username"])){
			if($_POST["form"] == "logout"){
				unset($_SESSION["username"]);
			}
			if($_POST["form"] == "addTimeline"){
				if(!empty($_POST["timeline"])){
					$timeline_array = explode("\n", $_POST["timeline"]);
					$detailed_timeline_array = array();
					$timeline_error = false;
					
					foreach($timeline_array as $key => $timestamp){
						// $detailed_timestamp is to be an array of [0] => HMS timestamp [1] => value [2] => url
						// example: [0] => "01:32:54" [1] => "The hosts talk about a topic" [2] => "http://www.relevanturl.com"
						$detailed_timestamp = explode(" ", $timestamp, 2); // Splits the line in two pieces by the first space
						if(strpos($detailed_timestamp[1], "http://") !== FALSE){ // Check for the existance of a URL (http)
							$detailed_timestamp[2] = strstr($detailed_timestamp[1], "http://");
							$detailed_timestamp[1] = strstr($detailed_timestamp[1], "http://", TRUE); // Removes url from timestamp value
						}
						if(strpos($detailed_timestamp[1], "https://") !== FALSE){ // Check for the existance of a URL (https)
							$detailed_timestamp[2] = strstr($detailed_timestamp[1], "https://");
							$detailed_timestamp[1] = strstr($detailed_timestamp[1], "https://", TRUE); // Removes url from timestamp value
						}
						// Convert timestamps from HMS form 01:32:54 to seconds only timestamp form 5574.
						if(count(explode(":", $detailed_timestamp[0])) == 3){
							$detailed_timeline_array[$key][0] = explode(":", $detailed_timestamp[0])[0]*3600 + explode(":", $detailed_timestamp[0])[1]*60 + explode(":", $detailed_timestamp[0])[2];
						} else if(count(explode(":", $detailed_timestamp[0])) == 2){
							$detailed_timeline_array[$key][0] = explode(":", $detailed_timestamp[0])[0]*60 + explode(":", $detailed_timestamp[0])[1];
						} else {
							$errors[] = "There was an error with the formatting of the timeline.";
						}
						// Remove whitespace from value and url fields.
						$detailed_timeline_array[$key][1] = trim($detailed_timestamp[1]);
						if(isset($detailed_timestamp[2])){
							$detailed_timeline_array[$key][2] = trim($detailed_timestamp[2]);
						}
					}
					if($timeline_error){
						$errors[] = "Please ensure all timestamps have a time, a topic, and optionally, a relevant url.";
					}
					
					// Submit the timeline data to the database.
					if(empty($errors)){
						foreach($detailed_timeline_array as $timestamp){
							if(isset($timestamp[2])){
								$current_episode->addTimestamp($timestamp[0], $timestamp[1], $timestamp[2]);							
							} else {
								$current_episode->addTimestamp($timestamp[0], $timestamp[1]);
							}
						}
						// Get ID of current episode from Identifier
						$stmt = $con->prepare("SELECT `id` FROM `episodes` WHERE `Identifier` = :identifier");
						$stmt->execute(array(":identifier" => $current_episode->getIdentifier()));
						$episode_id = $stmt->fetchAll()[0];
						$Log->Log($_POST["form"], $episode_id, json_encode($detailed_timeline_array));
					}
					
				} else {
					$errors[] = "Please enter a timeline.";
				}
			}
			if($_POST["form"] == "addTimelineRow"){
				if(empty($_POST["time"]) || empty($_POST["event"])){
					$errors[] = "Please ensure both a time and an event are submitted.";
				}
				if(count(explode(":", $_POST["time"])) == 3){
					$time = explode(":", $_POST["time"])[0]*3600 + explode(":", $_POST["time"])[1]*60 + explode(":", $_POST["time"])[2];
				} else if(count(explode(":", $_POST["time"])) == 2){
					$time = explode(":", $_POST["time"])[0]*60 + explode(":", $_POST["time"])[1];
				} else {
					$errors[] = "There was an error with the formatting of the timeline.";
				}
				
				if(empty($errors)){
					if(isset($_POST["url"])){
						$current_episode->addTimestamp($_POST["time"], $_POST["event"], $_POST["url"]);			
						$Log->Log($_POST["form"], $_POST["id"], json_encode(array($_POST["time"], $_POST["event"], $_POST["url"])));				
					} else {
						$current_episode->addTimestamp($_POST["time"], $_POST["event"]);
						$Log->Log($_POST["form"], $_POST["id"], json_encode(array($_POST["time"], $_POST["event"])));
					}
					$success = "Timeline row has been successfully added.";
				}
			}
			if($_POST["form"] == "updateTimestamp"){
				if(empty($_POST["timestamp"]) || empty($_POST["id"])){
					$errors[] = "Please ensure all fields have values.";
				}
				if(empty($errors)){
					$stmt = $con->prepare("SELECT * FROM `timestamps` WHERE `id`=:id");
					$stmt->execute(array(":id" => $_POST["id"]));
					$previous_values = $stmt->fetchAll()[0];
					$stmt = $con->prepare("UPDATE `timestamps` SET `Value` = :value, `URL`= :url WHERE `id`=:id ");
					$stmt->execute(array(
						"value" => $_POST["timestamp"],
						"url" => $_POST["url"],
						"id" => $_POST["id"]
					));
					$stmt = $con->prepare("SELECT * FROM `timestamps` WHERE `id`=:id");
					$stmt->execute(array(":id" => $_POST["id"]));
					$new_values = $stmt->fetchAll()[0];
					$Log->Log($_POST["form"], $_POST["id"], json_encode($new_values), json_encode($previous_values));
				}
			}
			if($_POST["form"] == "deleteTimestamp"){
				if(empty($_POST["id"])){
					$errors[] = "Please ensure the relevant timestamp id is submitted.";
				}
				if(empty($errors)){
					$stmt = $con->prepare("UPDATE `timestamps` SET `Deleted` = 1 WHERE `id`=:id");
					$result = $stmt->execute(array(
						":id" => $_POST["id"]
					));
					$Log->Log($_POST["form"], $_POST["id"]);
					if($result){
						$success = "The timeline row was successfully removed.";
					} else {
						$errors[] = "There has been a MySQL error.";
					}
				}
			}
		} else {
				if($_POST['form'] == "login"){
					if(empty($_POST["username"] || empty($_POST["password"]))){
						$errors[] = "Please enter both a username and a password.";
					}
					if(empty($errors)){				
						$stmt = $con->prepare("SELECT * FROM admins WHERE Username = :username");
						$stmt->execute(array(
							":username" => $_POST["username"],
						));
						$result = $stmt->fetchAll();
						if(password_verify($_POST["password"], $result[0]["Password"])){
							$_SESSION["username"] = $result[0]["ID"];
						} else {
							$errors[] = "The entered username or password is incorrect.";
						}
					}
				}
			}
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
	
	if ($current_episode->getTimelineAuthor() != "0") {
		$author = new Author($con, $current_episode->getTimelineAuthor());
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
		<meta name="description" content="<?php if ($source == "latest") { echo $Podcast->getDescription(); } else { echo "Guests: " . $guests_list; } ?>">
		<link rel="canonical" href="<?php echo $canonical; ?>">
		<link rel="alternate" type="application/rss+xml" title="<?php echo $Podcast->getName(); ?>" href="http://feeds.feedburner.com/<?php echo $Podcast->getFeedburner(); ?>">
		<link rel="search" type="application/opensearchdescription+xml" href="<?php echo $domain; ?>opensearchdescription.xml" title="<?php echo $Podcast->getTitle(); ?>">
		<title><?php if($source == "get") { echo "Episode #" . $current_episode->getNumber() . " · "; } ?><?php echo $Podcast->getTitle(); ?></title>
		
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
		<meta property="og:site_name" content="<?php echo $Podcast->getTitle(); ?>">

<?php
		if ($source == "get") {
?>
		<meta property="og:type" content="music.song">
		<meta property="og:title" content="<?php echo $Podcast->getName(); ?> #<?php echo $current_episode->getNumber(); ?>">
		<meta property="og:description" content="Guests: <?php echo $guests_list; ?>">
		<meta property="og:url" content="<?php echo $domain; ?>episode/<?php echo $current_episode->getNumber(); ?>">
		<meta property="og:audio" content="http://media.blubrry.com/painkilleralready/archive.org/download/<?php echo $current_episode->getIdentifier(); ?>/<?php echo str_replace("_", "-", strtolower($current_episode->getIdentifier())); ?>.mp3">
		<meta property="og:audio:type" content="audio/vnd.facebook.bridge">
		<meta property="music:album" content="<?php echo $domain; ?>">
		<meta property="music:album:track" content="<?php echo $current_episode->getNumber(); ?>">
		<meta property="music:duration" content="<?php echo $current_episode->getLength(); ?>">
<?php
			foreach ($hosts as $host) {
?>
		<meta property="music:musician" content="<?php echo $domain; ?>person/<?php echo $host->getID(); ?>">
<?php
			}
		
			foreach ($guests as $guest) {
?>
		<meta property="music:musician" content="<?php echo $domain; ?>person/<?php echo $guest->getID(); ?>">
<?php
			}
		} else {
?>
		<meta property="og:type" content="music.album">
		<meta property="og:title" content="<?php echo $Podcast->getName(); ?>">
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
		<meta property="twitter:domain" content="<?php echo $base_domain; ?>">
<?php

		if ($source == "get") {
?>
		<meta property="twitter:card" content="player">
		<meta property="twitter:title" content="<?php echo $Podcast->getName(); ?> #<?php echo $current_episode->getNumber(); ?>">
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
		<link rel="stylesheet" type="text/css" href="<?php echo $domain; ?>css/main.css?ver=<?php echo filemtime("css/main.css"); ?>">
        <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans:400,700">
		<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/3.0.2/css/font-awesome.min.css">
	</head>
	<body data-type="Episode">
<?php include_once("templates/header.php"); 
	
	if (!empty($errors)) {
		foreach ($errors as $error) {
?>
		<div class="error-message">
			<p><?php echo $error; ?></p>
		</div>
<?php
		}
	}
	
	if (isset($success)) {
?>
		<div class="success-message">
			<p><?php echo $success; ?></p>
		</div>
<?php
	}
?>
				<h2><?php echo $Podcast->getName(); ?> #<?php echo $current_episode->getNumber(); ?></h2>
				<div class="info">
					<span class="published" title="Date Published"><i class="icon-time"></i><small><time datetime="<?php echo $current_episode->getDate(); ?>"><?php echo date("F d, Y", strtotime($current_episode->getDate())); ?></time></small></span>
					<?php if ($current_episode->getReddit() != "") { ?><a class="comments" title="Discussion Comments" href="http://www.reddit.com/comments/<?php echo $current_episode->getReddit(); ?>"><i class="icon-comments"></i><small id="comments" data-reddit="<?php echo $current_episode->getReddit(); ?>">Comments</small></a><?php echo PHP_EOL; } ?>
					<?php if ($current_episode->getTimelineAuthor() != "0") { ?><a class="author" title="Timeline Author" href="<?php echo $author->getDisplayLink(); ?>"><i class="icon-user"></i><small><?php echo $author->getDisplayName(); ?></small></a><?php } ?>
				</div>
				<div id="rock-hardplace" class="clear"></div>
				<div id="video">
					<iframe src="https://www.youtube.com/embed/<?php echo $current_episode->getYouTube(); ?>?enablejsapi=1&amp;start=<?php if (isset($_GET["timestamp"])) { echo $_GET["timestamp"]; } ?>" allowfullscreen id="player"></iframe>
				</div>
				<div id="hosts" class="section items">
					<h4 class="section-header">Hosts</h4>
<?php

	foreach ($hosts as $host) {
?><a class="item" target="_blank" href="<?php echo $domain . "person/" . $host->getID(); ?>"><img alt="<?php echo $host->getName(); ?>" title="<?php echo $host->getName(); ?>" src="<?php echo (file_exists("img/people/" . $host->getID().".png")) ? $domain."/img/people/" . $host->getID() : $domain."/img/people/unknown" ?>.png"></a><?php
	}
	
?>
				</div>
<?php
	
	if (count($guests) > 0) {
?>
				<div id="guests" class="section items">
					<h4 class="section-header">Guests</h4>
<?php

	foreach ($guests as $guest) {
?><a class="item" target="_blank" href="<?php echo $domain . "person/" . $guest->getID(); ?>"><img alt="<?php echo $guest->getName(); ?>" title="<?php echo $guest->getName(); ?>" src="<?php echo (file_exists("img/people/" . $guest->getID().".png")) ? $domain."/img/people/" . $guest->getID() : $domain."/img/people/unknown" ?>.png"></a><?php		
	}

?>
				</div>
<?php
	}
	
	if (count($sponsors) > 0) {
?>
				<div id="sponsors" class="section items">
					<h4 class="section-header">Sponsors</h4>
<?php

		foreach ($sponsors as $sponsor) {
?><a class="item" target="_blank" href="<?php echo $domain . "person/" . $sponsor->getID(); ?>"><img alt="<?php echo $sponsor->getName(); ?>" title="<?php echo $sponsor->getName(); ?>" src="<?php echo (file_exists("img/people/" . $sponsor->getID().".png")) ? $domain."/img/people/" . $sponsor->getID() : $domain."/img/people/unknown" ?>.png"></a><?php		
		}

?>
				</div>
<?php
	}
?>
				<div class="clear"></div>

				<div id="timeline-clear" class="clear"></div>
<?php
	$timestamps = $current_episode->getTimestamps();
	if (count($timestamps) > 0) {
?>
				<div id="timeline-horizontal" class="section">
					<h4 class="section-header">Timeline</h4>
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
<?php
		if(isset($_SESSION["username"])){
?>
				<div id="addTimelineRow" class="section">
					<h4 class="section-header">Add Single Timeline Row</h4>
					<form action="<?php echo $domain; ?>index.php" method="post">
						<input type="text" name="time" id="time" placeholder="1:23:45" />
						<input type="text" name="event" id="event" placeholder="The hosts talk about a topic" />
						<input type="text" name="url" id="url" placeholder="http://www.relevanturl.com (optional)" />
						<input type="hidden" name="form" value="addTimelineRow" />
						<input type="submit" value="Add Timeline Row" />
					</form>
				</div>
<?php
		}
?>
				<table id="timeline-vertical" class="section">
					<thead>
						<tr>
<?php
		if(isset($_SESSION["username"])){
?>
							<th>Delete</th>
<?php
		}
?>
							<th>Time</th>
							<th>Event</th>
						</tr>
					</thead>
					<tbody>
<?php
		foreach ($timestamps as $timestamp) {
?>
						<tr>
<?php
			if(isset($timestamp["id"]) && isset($_SESSION["username"])){
?>
							<td class="delete">
								<form action="<?php echo $domain; ?>index.php" method="post">
									<input type="hidden" name="id" value="<?php echo $timestamp["id"]; ?>" />
									<input type="hidden" name="form" value="deleteTimestamp" />
									<input type="submit" value="&#9747;" />
								</form>
							</td>
<?php
			}
?>
							<td class="timestamp"><a class="timelink" href="<?php echo $domain . "episode/" . $current_episode->getNumber() . "?timestamp=" . $timestamp["Begin"]; ?>" data-begin="<?php echo $timestamp["Begin"]; ?>" data-end="<?php echo $timestamp["End"]; ?>"><?php echo $timestamp["HMS"]; ?></a></td>
							<td class="event">
<?php
			if(isset($timestamp["id"]) && isset($_SESSION["username"])){
?>
							<form action="index.php" method="post" class="updateTimestampForm" id="updateTimestampForm<?php echo $timestamp["id"]; ?>">
								<input type="text" name="timestamp" value="<?php echo $timestamp["Value"]; ?>" />
								<input type="text" name="url" value="<?php echo $timestamp["URL"]; ?>" placeholder="URL" />
								<input type="hidden" name="id" value="<?php echo $timestamp["id"]; ?>" />
								<input type="hidden" name="form" value="updateTimestamp" />
								<input type="submit" value="Update Timestamp" />
							</form>
<?php
			}
?>
							<span id="timestamp<?php echo $timestamp["id"]; ?>"><?php if ($timestamp["URL"] != "") { ?><a target="_blank" href="<?php echo $timestamp["URL"]; ?>"><?php echo $timestamp["Value"]; ?></a><?php } else { echo $timestamp["Value"]; } ?></span>
<?php
			if(isset($timestamp["id"]) && isset($_SESSION["username"])){
?>
							<button class="editTimestamp" id="<?php echo $timestamp["id"]; ?>">Edit Timestamp</button>
<?php
			}
?>
							</td>
						</tr>
<?php
		}
		
?>
					</tbody>
				</table>
<?php
	} else {
		if(isset($_SESSION["username"])){
?>
				<div id="Add Timeline" class="section">
					<h4 class="section-header">Add Timeline</h4>
					<form action="<?php echo $domain; ?>index.php" method="post">
						<textarea name="timeline" placeholder="23:45 The hosts talk about a topic<?php echo "\r\n"; ?>1:32:54 The hosts talk about a topic with a relevant website http://www.relevanturl.com"></textarea>
						<input type="hidden" name="form" value="addTimeline" />
						<input type="submit" value="Submit Timeline" />
					</form>
				</div>
<?php
		}
	}
		
	include_once("templates/footer.php");
?>
	</body>
</html>