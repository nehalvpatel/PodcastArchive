				<h2>{{ @Core->getName() }} #{{ @current_episode->getNumber() }}</h2>
				<div class="info">
					<span class="published" title="Date Published"><i class="icon-time"></i><small><time datetime="{{ @current_episode->getDate() }}">{{ date("F d, Y", strtotime(@current_episode->getDate())) }}</time></small></span>
					<check if="{{ @current_episode->getReddit() != '' }}"><a class="comments" title="Discussion Comments" href="http://www.reddit.com/comments/{{ @current_episode->getReddit() }}"><i class="icon-comments"></i><small id="comments" data-reddit="{{ @current_episode->getReddit() }}">Comments</small></a>{{ PHP_EOL }}</check>
					<check if="{{ @current_episode->getTimelineAuthor() !== false }}"><a class="author" title="Timeline Author" href="{{ @timeline_author->getDisplayLink() }}"><i class="icon-user"></i><small>{{ @timeline_author->getDisplayName() }}</small></a></check>
				</div>
				<div id="rock-hardplace" class="clear"></div>
				<div id="video">
					<iframe src="https://www.youtube.com/embed/{{ @current_episode->getYouTube() }}?enablejsapi=1&amp;start=<check if="{{ isset($_GET['timestamp']) }}">{{ $_GET['timestamp'] }}</check>" allowfullscreen id="player"></iframe>
				</div>
				<div id="hosts" class="section items">
					<h4 class="section-header">Hosts</h4>
					<repeat group="{{ @current_episode->getHosts() }}" value="{{ @host }}"><a class="item" target="_blank" href="{{ @domain }}person/{{ @host->getID() }}"><img alt="{{ @host->getName() }}" title="{{ @host->getName() }}" src="{{ @domain }}img/people/{{ @host->getID() }}.png"></a></repeat>
				</div>
				<check if="{{ count(@current_episode->getGuests()) > 0 }}">
				<div id="guests" class="section items">
					<h4 class="section-header">Guests</h4>
					<repeat group="{{ @current_episode->getGuests() }}" value="{{ @guest }}"><a class="item" target="_blank" href="{{ @domain }}person/{{ @guest->getID() }}"><img alt="{{ @guest->getName() }}" title="{{ @guest->getName() }}" src="{{ @domain }}img/people/{{ @guest->getID() }}.png"></a></repeat>
				</div>
				</check>
				<check if="{{ count(@current_episode->getSponsors()) > 0 }}">
				<div id="sponsors" class="section items">
					<h4 class="section-header">Sponsors</h4>
					<repeat group="{{ @current_episode->getSponsors() }}" value="{{ @sponsor }}"><a class="item" target="_blank" href="{{ @domain }}person/{{ @sponsor->getID() }}"><img alt="{{ @sponsor->getName() }}" title="{{ @sponsor->getName() }}" src="{{ @domain }}img/people/{{ @sponsor->getID() }}.png"></a></repeat>
				</div>
				</check>
				<div id="timeline-clear" class="clear"></div>
				<check if="{{ @current_episode->getTimelined() }}">
				<div id="timeline-horizontal" class="section">
					<h4 class="section-header">Timeline</h4>
					<div class="timeline">
					<repeat group="{{ @current_episode->getTimestamps() }}" value="{{ @timestamp }}">
						<a class="timelink" href="{{ @domain }}episode/{{ @current_episode->getNumber() }}?timestamp={{ @timestamp->getBegin() }}" data-begin="{{ @timestamp->getBegin() }}" data-end="{{ @timestamp->getEnd() }}">
							<div class="topic" style="width: {{ @timestamp->getWidth() }}%">
								<div class="tooltip<check if="{{ @timestamp->getBegin() > (@current_episode->getYouTubeLength()) / 2 }}"> right</check>" id="{{ @timestamp->getID() }}">
									<div class="triangle"></div>
									<span>{{ @timestamp->getValue() }}</span>
								</div>
							</div>
						</a>
					</repeat>
					</div>
				</div>
				<table id="timeline-vertical" class="section">
					<thead>
						<tr>
							<th>Time</th>
							<th>Event</th>
						</tr>
					</thead>
					<tbody>
					<repeat group="{{ @current_episode->getTimestamps() }}" value="{{ @timestamp }}">
						<tr>
							<td class="timestamp"><a class="timelink" href="{{ @domain }}episode/{{ @current_episode->getNumber() }}?timestamp={{ @timestamp->getBegin() }}" data-begin="{{ @timestamp->getBegin() }}" data-end="{{ @timestamp->getEnd() }}">{{ @timestamp->getTime() }}</a></td>
							<td class="event"><check if="{{ @timestamp->getURL() }}"><true><a target="_blank" href="{{ @timestamp->getURL() }}">{{ @timestamp->getValue() }}</a></true><false>{{ @timestamp->getValue() }}</false></check></td>
						</tr>
					</repeat>
					</tbody>
				</table>
				</check>