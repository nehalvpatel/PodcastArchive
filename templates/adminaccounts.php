<h2>Admin Accounts</h2>
<div id="form">
	<div id="title">
		<h3>Add an Admin Account</h3>
	</div>
	<div id="fields">
		<form method="POST">
			<input type="text" placeholder="Username" name="username" />
			<input type="password" placeholder="Password" name="password" />
			<input type="hidden" name="form" value="addadminaccount" />
			<input type="submit" value="Add Admin Account" />
		</form>
	</div>
</div>
<div id="form">
	<div id="title">
		<h3>Change an Admin Password</h3>
	</div>
	<div id="fields">
		<form method="POST">
			<input type="text" placeholder="Username" name="username" />
			<input type="password" placeholder="Previous Password" name="previouspassword" />
			<input type="password" placeholder="New Password" name="newpassword" />
			<input type="hidden" name="form" value="changeadminpassword" />
			<input type="submit" value="Change Admin Password" />
		</form>
	</div>
</div>