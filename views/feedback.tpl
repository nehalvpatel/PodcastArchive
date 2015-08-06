				<include if="{{ isset(@success) }}" href="../views/feedback/success.tpl" />
				<include if="{{ isset(@errors) }}" href="../views/feedback/errors.tpl" />
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