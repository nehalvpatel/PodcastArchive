<div class="module">
	<div class="module_title">
		<h2>Add Timestamp</h2>
	</div>
	<div class="module_body">
		<form method="POST">
			<table>
				<tbody>
					<tr>
						<td><label for="episode">Episode</label></td>
						<td><input type="text" required id="episode" name="episode" placeholder="Example: 156" <?php echo (isset($_POST['episode'])) ? 'value="'.$_POST['episode'].'" ' : null; ?>></td>
					</tr>
					<tr>
						<td><label for="timestamp">Timestamp</label></td>
						<td><input type="text" required id="timestamp" name="timestamp" placeholder="Example: 02:53:39"></td>
					</tr>
					<tr>
						<td><label for="value">Value</label></td>
						<td><input type="text" required id="value" name="value"></td>
					</tr>
					<tr>
						<td><label for="url">URL</label></td>
						<td><input type="text" id="url" name="url" placeholder="Optional"></td>
					</tr>
				</tbody>
			</table>
			<input type="hidden" name="form" value="addtimestamp">
			<input type="submit" name="submit" id="submit" value="Add Timestamp">
		</form>
	</div>
</div>