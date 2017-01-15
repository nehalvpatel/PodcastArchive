				<div id="page-title">
					<h2>Developers and Contributors</h2>
					<p>Thank you to everyone for making this website such a huge success. This website would not have become what it is without the immense effort our developers and contributors donate.</p>
				</div>
				<div class="section">
					<h3>Developers</h3>
					<ul>
						<repeat group="{{ @developers }}" value="{{ @developer }}">
						<li><a href="{{ @developer->getDisplayLink() }}">{{ @developer->getDisplayName() }}</a>, {{ @developer->getPraise() }}.</li>
						</repeat>
					</ul>
				</div>
				<div class="section">
					<h3>Contributors</h3>
					<ul>
						<repeat group="{{ @contributors }}" value="{{ @contributor }}">
						<li><a href="{{ @contributor->getDisplayLink() }}">{{ @contributor->getDisplayName() }}</a>, {{ @contributor->getPraise() }}.</li>
						</repeat>
					</ul>
				</div>