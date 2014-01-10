<div class="module">
	<div class="module_title">
		<h2>Add Timeline</h2>
	</div>
	<div class="module_body">
		<form method="POST">
			<table>
				<tbody>
					<tr>
						<td><label for="episode">Episode</label></td>
						<td><input type="text" required id="episode" name="episode" placeholder="Example: 156"></td>
					</tr>
					<tr>
						<td><label for="timeline_textbox">Timeline</label></td>
						<td><textarea required id="timeline_textbox" name="timeline"></textarea></td>
					</tr>
				</tbody>
			</table>
			<input type="hidden" name="form" value="addtimeline">
			<input type="submit" name="submit" id="submit" value="Add Timeline">
		</form>
	</div>
</div>