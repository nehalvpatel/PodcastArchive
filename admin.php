<?php
	session_start();
	require_once('config.php');
	require_once('class.admin.php');
	$Admin = new Admin($con);
	
	// Process form submissions
	$errors = array();
	
	if(!empty($_POST)){
		if(isset($_SESSION['admin'])){ // User is currently logged in.
			if($_POST['form'] == 'addepisode'){
				
			}
			if($_POST['form'] == 'addtimestamp'){
				$timestamp = $_POST['timestamp_hours'].':'.$_POST['timestamp_minutes'].':'.$_POST['timestamp_seconds'];
				$result = $Admin->addTimestamp($timestamp, $_POST['value'], $_POST['episode']);
				if($result === true){
					$success = 'Timestamp was successfully added to the database.';
				} else {
					$errors = $result;
				}
			}
			if($_POST['form'] == 'addtimeline'){
				$result = $Admin->addTimeline($_POST['episode'], $_POST['timeline']);
				if($result === true){
					$success = 'Timeline was successfully added to the database.';
				} else {
					$errors = $result;
				}
			}
		} else { // User is not currently logged in.
			if($_POST['form'] == 'login'){
				$result = $Admin->doLogin($_POST['username'], $_POST['password']);
				if($result === true){
					$_SESSION['admin'] = $_POST['username'];
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
		<link rel="stylesheet" type="text/css" href="css/admin.css" />
	</head>
	<body>
		<div id="top_bar">
			<h1>Admin Panel</h1>
<?php
if(isset($_SESSION['admin'])){
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
<?php                // Success and Error handling.
	if(isset($success)){
?>
		<div class="success">
			<p><?php echo $success; ?></p>
		</div>
<?php
	}
	if(!empty($errors)){
		foreach($errors as $error){
?>
		<div class="error">
			<p><?php echo $error; ?></p>
		</div>
<?php
		}
	}

	if(isset($_GET['module'])){
		switch($_GET['module']){
			case 'addepisode':
				require('templates/addepisode.php');
			break;
			case 'addtimestamp':
				require('templates/addtimestamp.php');
			break;
			case 'addtimeline':
				require('templates/addtimeline.php');
			break;
			default:
				require('template/admin_general');
			break;
		}
	}
	if(!isset($_SESSION['admin'])){
		require('templates/login.php');
	}
?>
	</body>
</html>
