<?php

	require_once("config.php");
	
	if (isset($_POST["issue"], $_POST["explanation"]) && !empty($_POST["issue"]) && !empty($_POST["explanation"])) {		
		$issueTypes = array(
			"timeline_typo",
			"browser_rendering",
			"website_content",
			"other"
		);
		
		if (!in_array($_POST["issue"], $issueTypes)) {
			$errors[] = "Please select a valid issue type.";
		}
		
		if (strlen($_POST["explanation"]) > 3000) {
			$errors[] = "Please make sure that your explanation isn't too long.";
		}
		
		if (empty($errors)) {
			$query = $con->prepare("INSERT INTO `feedback` (`issue`,`explanation`) VALUES (:issue, :explanation)");
			$result = $query->execute(array(
				"issue" 		=> $_POST["issue"],
				"explanation" 	=> $_POST["explanation"]
			));
			if($result){
				$success = "Thank you, your feedback has been received and our administrators will now work to solve the problem shortly.";
			} else {
				$errors[] = "There was a MySQL error, please try again.";
			}
		}					
	} else {
		$errors[] = "Please make sure you selected an issue and filled out the explanation.";
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
		<link rel="canonical" href="<?php echo $domain; ?>feedback">
		<title>Feedback · <?php echo $Podcast->getTitle(); ?></title>
		
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
		
		<!-- CSS -->
		<link rel="stylesheet" type="text/css" href="<?php echo $domain; ?>css/main.css?ver=<?php echo $commit_count; ?>">
		<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/3.0.2/css/font-awesome.min.css">
	</head>
	<body>
<?php

	include_once("templates/header.php");
	
	if (isset($success)) {
?>
		<div class="success-message">
			<p><?php echo $success; ?></p>
		</div>
<?php
	}
	
	if (!empty($errors)) {
		foreach ($errors as $error) {
?>
		<div class="error-message">
			<p><?php echo $error; ?></p>
		</div>
<?php
		}
	}
?>
				<div id="page-title">
					<h2>Feedback</h2>
					<p>Thank you for helping us improve our website. We apologise for any way our website may have inconvenienced you.</p>
				</div>
				<form method="POST">
					<div class="section">
						<h3>Issue</h3>
						<div>
							<input type="radio" name="issue" value="timeline_typo" id="timeline_typo">
							<label for="timeline_typo">A spelling/grammar/punctuation/timing mistake in the website's timelines.</label>
							<br>
							<input type="radio" name="issue" value="browser_rendering" id="browser_rendering">
							<label for="browser_rendering">A problem with browser rendering (the website doesn't look right).</label>
							<br>
							<input type="radio" name="issue" value="website_content" id="website_content">
							<label for="website_content">A problem with the content on our website.</label>
							<br>
							<input type="radio" name="issue" value="other" id="otherwise">
							<label for="otherwise">Other</label>
						</div>
					</div>
					<div class="section">
						<h3>Explain</h3>
						<div>
							<textarea name="explanation" id="explanation" rows="5"></textarea>
						</div>
					</div>
					<input type="submit" value="Submit Feedback">
				</form>
<?php include_once("templates/footer.php"); ?>
	</body>
</html>