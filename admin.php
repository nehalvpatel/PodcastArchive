<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Admin</title>
		<style type="text/css">
			.label {
				text-align: right;
				width: 50%;
			}
			input {
				width: 100%;
				box-sizing: border-box;
			}
			select, .form-button {
				width: 100%;
			}
		</style>
	</head>
	<body>
<?php

	session_start();
	require_once("config.php");
	
	if (!isset($_SESSION["Admin"])) {
		if (!isset($_POST["submit"])) {
loginform:
?>
		<form method="POST">
			<table>
				<tbody>
					<tr>
						<td class="label"><label for="username">Username:</label></td>
						<td><input type="text" id="username" name="username" required></td>
					</tr>
					<tr>
						<td class="label"><label for="password">Password:</label></td>
						<td><input type="password" id="password" name="password" required></td>
					</tr>
					<tr>
						<td colspan="2"><input class="form-button" type="submit" name="submit" value="Login"></td>
					</tr>
				</tbody>
			</table>
		</form>
	</body>
</html>
<?php
		} else {
			if ((empty($_POST["username"])) || (empty($_POST["password"]))) {
				echo "Not all fields were filled in<br><br>";
				goto loginform;
			} else {
				$login_query = $con->prepare("SELECT `username` FROM `admins` WHERE `username` = :username AND `password` = :password");
				$login_query->execute(
					array(
						":username" => $_POST["username"],
						":password" => hash('sha512', $_POST['password'] . '305yh83],>')
					)
				);
				$login_results = $login_query->fetchAll();
				
				if (count($login_results) > 0) {
					$_SESSION["Admin"] = $login_results[0]["username"];
					goto timestampform;
				} else {
					echo "Invalid credentials<br><br>";
					goto loginform;
				}
			}
		}
	} else {
		if (!isset($_POST["submit"])) {
timestampform:
?>
		<form method="POST">
			<table>
				<tbody>
					<tr>
						<td class="label"><label for="episode">Episode:</label></td>
						<td>
							<select id="episode" name="episode">
<?php

	foreach ($Podcast->getEpisodes() as $episode_name => $episode) {
?>
								<option value="<?php echo $episode_name; ?>"><?php echo $episode_name; ?></option>
<?php	
	}
	
?>
							</select>
						</td>
					</tr>
					<tr>
						<td></td>
						<td><br></td>
					</tr>
					<tr>
						<td class="label"><label for="type">Type:</label></td>
						<td>
							<select id="type" name="type">
								<option value="Text">Text</option>
								<option value="Link">Link</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="label"><label for="timestamp">Timestamp:</label></td>
						<td><input type="text" id="timestamp" name="timestamp" placeholder="hh:mm:ss" required></td>
					</tr>
					<tr>
						<td class="label"><label for="value">Value:</label></td>
						<td><input type="text" id="value" name="value" required></td>
					</tr>
					<tr>
						<td class="label"><label for="url">URL:</label></td>
						<td><input type="text" id="url" name="url" placeholder="Optional for Text"></td>
					</tr>
					<tr>
						<td colspan="2"><input class="form-button" type="submit" name="submit" value="Add"></td>
					</tr>
				</tbody>
			</table>
		</form>
	</body>
</html>
<?php
		} else {
			if ((empty($_POST["episode"])) || (empty($_POST["type"])) || (empty($_POST["timestamp"])) || (empty($_POST["value"])) || (empty($_POST["url"]))) {
				echo "Not all fields were filled in<br><br>";
				goto timestampform;
			} else {
				$pattern = "/^(?:(?:([01]?\d|2[0-3]):)?([0-5]?\d):)?([0-5]?\d)$/";
				preg_match($pattern, substr($_POST["timestamp"], 3), $matches, PREG_OFFSET_CAPTURE);
				
				if (count($matches) == 0) {
					echo "Invalid timestamp<br><br>";
					goto timestampform;
				} else {
					$Episode = new Episode($_POST["episode"], $con);
					
					sscanf($_POST["timestamp"], "%d:%d:%d", $hours, $minutes, $seconds);
					$time_seconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
					
					$Episode->addTimestamp($_POST["type"], $time_seconds, $_POST['value'], $_POST["url"]);
					
					echo "Added timestamp<br><br>";
					goto timestampform;
				}
			}
		}
	}

?>