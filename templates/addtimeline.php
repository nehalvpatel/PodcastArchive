<h2>Add Timeline</h2>
<div id="form">
	<div id="title">
		<h3>Add Timeline to Episode #<?php echo $_GET['episode']; ?></h3>
	</div>
	<div id="fields">
		<form method="POST">
			<textarea name="timeline" placeholder="Insert Timeline Here"></textarea>
			<input type="hidden" name="form" value="addtimeline">
			<input type="submit" value="Add Timeline">
		</form>
	</div>
</div>
