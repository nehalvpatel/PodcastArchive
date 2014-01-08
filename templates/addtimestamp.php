<div class="module">
	<div class="module_title">
		<h2>Add Timestamp</h2>
	</div>
	<div class="module_body">
		<form method="post">
			<div class="input">
				<div class="label">
					<label for="episode">Episode</label>
				</div>
				<div class="input_box">
					<input type="text" id="episode" name="episode" placeholder="Example: 156" <?php echo (isset($_POST['episode'])) ? 'value="'.$_POST['episode'].'" ' : null; ?>>
				</div>
				<div class="clear"></div>
			</div>
			<div class="input">
				<div class="label">
					<label for="timestamp_hours">Timestamp</label>
				</div>
				<div class="input_box">
					<input type="text" id="timestamp_hours" name="timestamp_hours" />:
					<input type="text" id="timestamp_minutes" name="timestamp_minutes" />:
					<input type="text" id="timestamp_seconds" name="timestamp_seconds" />
				</div>
				<div class="clear"></div>
			</div>
			<div class="input">
				<div class="label">
					<label for="value">Value</label>
				</div>
				<div class="input_box">
					<input type="text" id="value" name="value">
				</div>
				<div class="clear"></div>
			</div>
			<input type="hidden" name="form" value="addtimestamp" />
			<input type="submit" value="Add Timestamp" />
		</form>
	</div>
</div>