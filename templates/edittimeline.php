<h2>Edit Timeline</h2>
<div id="form">
	<div id="title">
		<h3>Edit Timeline of Episode #<?php echo $_GET['episode']; ?></h3>
	</div>
	<div id="fields">
		<form method="POST">
			<textarea name="timeline">THIS IS IN CONSTRUCTION HENCE THE ERROR:

<?php $episode->getTimeline(); ?></textarea>
			<input type="hidden" name="form" value="edittimeline">
			<input type="submit" value="Edit Timeline">
		</form>
	</div>
</div>
