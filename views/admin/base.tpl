<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>{{ @page }} &middot; {{ @title }}</title>
		<link rel="stylesheet" type="text/css" href="{{ @domain }}css/admin.css">
	</head>
	<body>
		<h1>{{ @title }}</h1>
		<div id="navigation">
			<ul>
			<check if="{{ @loggedIn }}">
				<true>
					<li><a href="{{ @domain }}admin/home">Home</a></li>
					<li><a href="{{ @domain }}admin/viewepisodes">View Episodes</a></li>
					<li><a href="{{ @domain }}admin/addepisode">Add Episode</a></li>
					<li><a href="https://www.google.com/analytics/web/#report/visitors-overview/a46640110w77695213p80320716/">View Statistics</a></li>
					<li><a href="{{ @domain }}admin/accounts">Admin Accounts</a></li>
					<li><a href="{{ @domain }}admin/logout">Logout</a></li>
				</true>
				<false>
					<li class="active"><a href="admin.php">Login</a></li>
				</false>
			</check>
			</ul>
		</div>
		<div id="main">
		<check if="{{ isset(@success) }}">
			<div class="success">
				<p>{{ @success }}</p>
			</div>
		</check>
		<check if="{{ !empty(@errors) }}">
			<repeat group="{{ @errors }}" value="{{ @error }}">
				<div class="error">
					<p>{{ @error }}</p>
				</div>
			</repeat>
		</check>
		<check if="{{ @loggedIn }}">
			<true>
				<include href="{{ 'views/admin/' . @type . '.tpl' }}" />
			</true>
			<false>
				<h2>Login</h2>
				<div id="form">
					<div id="title">
						<h3>Login to the Admin Panel</h3>
					</div>
					<div id="fields">
						<form method="POST" action="{{ @domain }}admin/login">
							<input type="text" name="username" placeholder="Username">
							<input type="password" name="password" placeholder="Password">
							<input type="submit" value="Login">
						</form>
					</div>
				</div>
			</false>
		</check>
		</div>
	</body>
</html>