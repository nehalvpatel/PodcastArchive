<?php

	require_once("config.php");
	if(!empty($_POST)){
		$errors = array();
		if(strlen($_POST["issue"] > 100 || strlen($_POST["explanation"]) > 3000)){
			$errors[] = "Please make sure that your inputs aren't too large.";
		}
		if(empty($errors)){
			$query = $con->prepare("INSERT INTO `feedback` (`issue`,`explanation`) VALUES (:issue, :explanation)");
			$result = $query->execute(array(
				"issue" 		=> $_POST["issue"],
				"explanation" 	=> $_POST["explanation"]
			));
			if($result){
				$success = "Thank you, your feedback has been received and out administrators will now work to solve the problem shortly.";
			} else {
				$errors[] = "There was a MySQL error, please try again.";
			}
		}
	}

	if (isset($_SERVER["HTTP_USER_AGENT"]) && (strpos($_SERVER["HTTP_USER_AGENT"], "MSIE") !== false)) header("X-UA-Compatible: IE=edge,chrome=1");
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Meta -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<meta name="description" content="">
		<link rel="canonical" href="">
		<title>Developers and Contributors &middot; Painkiller Already Archive</title>
		
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
		<link rel="stylesheet" type="text/css" href="<?php echo $domain; ?>css/feedback.css?ver=<?php echo $commit_count; ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo $domain; ?>css/fontawesome.css?ver=<?php echo $commit_count; ?>">
		
		<!-- IE8 -->
		<!--[if lt IE 9]>
			<link rel="stylesheet" href="<?php echo $domain; ?>css/fontawesome-ie7.css">
			<script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7/html5shiv.js"></script>
			<script src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.js"></script>
		<![endif]-->
	</head>
	<body>
<?php include_once("templates/header.php"); ?>
			<div id="container">
				<p>Thank you for helping us improve our website. We apologise for any way our website may have inconvenienced you.</p>
				<div class="section">
<?php // Success and Error handling.
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
					<h2 class="section-header">Feedback Form</h2>
					<form method="post">
						<p>My issue relates to:<p>
						<input type="radio" name="issue" value="timeline_typo" id="timeline_typo"/>
						<label for="timeline_typo">A spelling/grammar/punctuation/timing mistake in the website's timelines.</label>
						<br />
						<input type="radio" name="issue" value="browser_rendering" id="browser_rendering"/>
						<label for="browser_rendering">A problem with browser rendering (the website doesn't look right).</label>
						<br />
						<input type="radio" name="issue" value="website_content" id="website_content"/>
						<label for="website_content">A problem with the content on our website.</label>
						<div id="otherwise">
							Otherwise, please specify:
							<br />
							<textarea name="issue_specified"></textarea>
						</div>
						<p>Please Explain the Issue</p>
						<textarea name="explanation" id="explanation"></textarea>
						<input type="submit" value="Submit Feedback" />
					</form>
				</div>
			</div>
<?php include_once("templates/footer.php"); ?>
	</body>
</html>