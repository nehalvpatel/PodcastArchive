<?php
	
	session_start();
	require_once("config.php");
	require_once("class.admin.php");
	$Admin = new Admin($con);
	
	// Process form submissions
	$errors = array();
	
	if (!empty($_POST)) {
		if ($Admin->isLoggedIn()) { // User is currently logged in.
			if ($_POST["form"] == "addepisode") {
				
			} elseif ($_POST["form"] == "addtimestamp") {
				if (isset($_POST["url"]) && !empty($_POST["url"])) {
					$url = $_POST["url"];
				} else {
					$url = "";
				}
				
				if ((empty($_POST["episode"]) && $_POST["episode"] !== "0") || empty($_POST["timestamp"]) || empty($_POST["value"])) {
					$errors[] = "Please make sure all fields were filled in.";
				} else {
					if ($Admin->validateTimestamp($_POST["timestamp"]) === FALSE) {
						$errors[] = "The timestamp is invalid.";
					} else {
						$timestamp = $Admin->convertTimestamp($_POST["timestamp"]);
						
						try {
							$episode = new Episode($con);
							$episode->initWithNumber($Podcast->trimEpisodeNumber($_POST["episode"]));
							
							if ($episode->addTimestamp($timestamp, $_POST["value"], $url, "0") === TRUE) {
								$success = "Timestamp was successfully added to the database.";
							}
						} catch (Exception $e) {
							$errors[] = $e->getMessage();
						}
					}
				}
			} elseif ($_POST["form"] == "addtimeline") {
				$episode_input = $_POST["episode"];
				$value = $_POST["timeline"];
				
				if((empty($episode_input) && $episode_input !== "0") || empty($value)){
					$errors[] = "Please make sure all fields were filled in.";
				} else {
					try {
						$episode = new Episode($con);
						$episode->initWithNumber($Podcast->trimEpisodeNumber($episode_input));
						
						$timestamps = preg_split("/\r\n|\n|\r/", $value);
						foreach ($timestamps as $timestamp) {
							$explosion = explode(" - ", $timestamp);
							
							$time = trim($explosion[0]);
							$event = trim($explosion[1]);
							
							if ($Admin->validateTimestamp($time) === FALSE) {
								$errors[] = "The timestamp is invalid.";
							} else {
								$timestamp = $Admin->convertTimestamp($time);
								$episode->addTimestamp($timestamp, $event, "Text");
							}
						}
					} catch (Exception $e) {
						$errors[] = $e->getMessage();
					}
					
					if (count($errors) == 0) {
						$success = "Timeline was successfully added to the database.";
					}
				}
			}
		} else { // User is not currently logged in.
			if ($_POST["form"] == "login") {
				$result = $Admin->doLogin($_POST["username"], $_POST["password"]);
				if ($result === true) {
					$_SESSION["admin"] = $_POST["username"];
				} else {
					$errors = $result;
				}
			}
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Admin Panel</title>
		<link rel="stylesheet" type="text/css" href="css/admin.css">
	</head>
	<body>
		<div id="top_bar">
			<h1>Admin Panel</h1>
<?php
	if ($Admin->isLoggedIn()) {
?>
			<p>Welcome to the admin panel!</p>
<?php
	}
?>
		</div>
		<div id="navigation">
			<ul>
				<li><a href="admin.php?module=addtimestamp">Add Timestamp</a></li>
				<li><a href="admin.php?module=addtimeline">Add Timeline</a></li>
			</ul>
		</div>
<?php // Success and Error handling.
	if (isset($success)) {
?>
		<div class="success">
			<p><?php
		echo $success;
?></p>
		</div>
<?php
	}
	
	if (!empty($errors)) {
		foreach ($errors as $error) {
?>
		<div class="error">
			<p><?php
			echo $error;
?></p>
		</div>
<?php
		}
	}
	
	if (isset($_GET["module"]) && $Admin->isLoggedIn()) {
		switch ($_GET["module"]) {
			case "addepisode":
				require("templates/addepisode.php");
				break;
			case "addtimestamp":
				require("templates/addtimestamp.php");
				break;
			case "addtimeline":
				require("templates/addtimeline.php");
				break;
			default:
				require("template/admin_general");
				break;
		}
	}
	
	if (!isset($_SESSION["admin"])) {
		require("templates/login.php");
	}
	
?>
	</body>
</html>