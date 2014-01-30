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
				$episode_input = $_GET["episode"];
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
			} elseif($_POST['form'] == "addadminaccount"){
				if($Admin->addAdminAccount($_POST['username'], $_POST['password'])){
					$success = "Admin account has been successfully added.";
				} else {
					$errors = $Admin->getErrors();
				}
			} elseif($_POST['form'] == "changeadminpassword"){
				if($Admin->changeAdminPassword($_POST['username'], $_POST['previouspassword'], $_POST['newpassword'])){
					$success = "Admin password has been succcessfully changed.";
				} else {
					$errors = $Admin->getErrors();
				}
			}
		} else { // User is not currently logged in.
			if ($_POST["form"] == "login") {
				if ($Admin->doLogin($_POST["username"], $_POST["password"])) {
					$_SESSION["admin"] = $_POST["username"];
				} else {
					$errors = $Admin->getErrors();
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
		<h1>Admin Panel</h1>
		<div id="navigation">
			<ul>
<?php
	if($Admin->isLoggedIn()){
?>
				<li><a href="index.php">Home</a></li>
				<li><a href="admin.php?module=viewepisodes">View Episodes</a></li>
				<li><a href="admin.php?module=addepisode">Add Episode</a></li>
				<li><a href="https://www.google.com/analytics/web/#report/visitors-overview/a46640110w77695213p80320716/">View Statistics</a></li>
				<li><a href="admin.php?module=adminaccounts">Admin Accounts</a></li>
<?php
	} else {
?>
				<li class="active"><a href="admin.php">Login</a></li>
<?php
	}
?>
			</ul>
		</div>
		<div id="main">
<?php // Success and Error handling.
	if (isset($success)) {
?>
		<div class="success">
			<p><?php echo $success; ?></p>
		</div>
<?php
	}
	
	if (!empty($errors)) {
		foreach ($errors as $error) {
?>
		<div class="error">
			<p><?php echo $error; ?></p>
		</div>
<?php
		}
	}
	
	if ($Admin->isLoggedIn()) {
		if(isset($_GET["module"])){
			switch ($_GET["module"]) {
				case "viewepisodes":
					require("templates/viewepisodes.php");
					break;
				case "adminaccounts":
					require("templates/adminaccounts.php");
					break;
				case "addtimeline":
					require("templates/addtimeline.php");
					break;
				case "edittimeline":
					require("templates/edittimeline.php");
					break;
			}
		}
	}
	
	if (!isset($_SESSION["admin"])) {
		require("templates/login.php");
	}
	
?>
		</div>
	</body>
</html>