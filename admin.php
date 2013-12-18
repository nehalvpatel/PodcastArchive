<?php

	require_once("config.php");
	if(!empty($_POST)){
		if($_POST['form'] == 'addTimestamp'){
			// !!! Need constructor parameters
			$Episode = new Episode();
			// Convert timestamp into seconds.
			$timestamp = explode($_POST['timestamp'], ':')[0]*3600+explode($_POST['timestamp'], ':')[1]*60+explode($_POST['timestamp'], ':')[2];
			// Make sure episode is formatted correctly.
			if(substr($_POST['episode'],0,4) == 'PKA_'){
				$episode = $_POST['episode'];
			} else {
				$episode = 'PKA_'.$_POST['episode'];
			}
			$Episode->AddTimestamp('Text', $episode, $timestamp, $_POST['value']);
		}
		if($_POST['form'] == 'login'){
			$errors = array();
			if(empty($_POST['username'])){
				$errors[] = 'Please make sure you have filled in the username field.';
			}
			if(empty($_POST['password'])){
				$errors[] = 'Please make sure you have filled in the password field.';
			}
			if(empty($errors)){
				$stmt = $con->prepare('SELECT `username` FROM `admins` WHERE `username`=:username AND `password`=:password');
				$stmt->execute(array(
					'username' => $_POST['username'],
					'password' => hash('sha512', $_POST['password'].'305yh83],>')
				));
				if($stmt->rowCount()>0){
					$_SESSION['admin'] = $_POST['username'];
				} else {
					$errors[] = 'Your username or password is incorrect';
				}
			}
		}
	}
?>
<html>
	<head>
		<title>Painkiller Already Admin Panel</title>
	</head>
	<body>
<?php
	if(isset($_SESSION['admin'])){
?>
		<form action="admin.php" method="post">
			<input type="text" placeholder="Episode" name="episode" <?php echo (!empty($_POST['episode'])) ? 'value="'.$_POST['episode'].'"' : null; ?>/>
			<br />
			<input type="text" placeholder="Time" name="timestamp" autofocus/>
			<input type="text" placeholder="Label" name="value" />
			<br />
			hh:mm:ss
			<br />
			<input type="hidden" name="form" value="addTimestamp" />
			<input type="submit" />
		</form>
<?php
	} else {
		if(!empty($errors)){
			foreach($errors as $error){
				echo $error, '<br />';
			}
		}
?>
		<form action="admin.php" method="post">
			<input type="text" name="username" placeholder="Username" />
			<input type="password" name="password" placeholder="Password" />
			<input type="hidden" name="form" value="login" />
			<input type="submit" value="Log in" />
		</form>
<?php
	}
?>
	</body>
</html>