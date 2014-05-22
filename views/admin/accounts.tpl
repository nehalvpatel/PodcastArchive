<h2>Accounts</h2>
	<check if="{{ @adminType == 0 }}">
		<div id="form">
			<div id="title">
				<h3>Add an Admin Account</h3>
			</div>
			<div id="fields">
				<form method="POST">
					<input type="text" placeholder="Username" name="username" />
					<input type="password" placeholder="Password" name="password" />
					<input type="hidden" name="form" value="add" />					
					<select name="type" style="width: 100%;">
						<option value="0" selected="selected">Administrator</option>
						<option value="1">Moderator</option>
					</select>
					<input type="submit" value="Add Account" />
				</form>
			</div>
		</div>
	</check>
	<div id="form">
		<check if="{{ @adminType == 0 }}">
			<true>
				<div id="title">
					<h3>Change an Admin Password</h3>
				</div>
				<div id="fields">
					<form method="POST">
						<input type="text" placeholder="Username" name="username" value="{{ @username }}" />
						<input type="password" placeholder="New Password" name="password" />
						<input type="hidden" name="form" value="change" />
						<input type="submit" value="Change Password" />
					</form>
				</div>
			</true>
			<false>
				<div id="title">
					<h3>Change Your Password</h3>
				</div>
				<div id="fields">
					<form method="POST">
						<input type="password" placeholder="Current Password" name="oldpass" />
						<input type="password" placeholder="New Password" name="newpass" />
						<input type="hidden" name="form" value="change" />
						<input type="submit" value="Change Password" />
					</form>
				</div>
			</false>
		</check>
	</div>