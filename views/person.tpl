                <div id="image">
                    <img id="person-image" class="person-image" alt="{{ @current_person->getName() }}" title="{{ @current_person->getName() }}" src="<?php echo $domain; ?>img/people/{{ @current_person->getID() }}a.png" />
                </div>
                <div id="details">
                    <div id="overview" class="section">
                        <h2 class="section-header">{{ @current_person->getName() }}</h2>
                        <p>{{ @current_person->getOverview() }}</p>
                    </div>
                    <check if="{{ count(@social_links) > 0 }}">
                    <div id="social-icons" class="section items">
                        <h2 class="section-header">Social</h2>
                        <repeat group="{{ @social_links }}" value="{{ @social_link }}"><a class="item" href="{{ @social_link['Link'] }}"><img alt="{{ @social_link['Name'] }}" title="{{ @social_link['Name'] }}" src="{{ @domain }}img/{{ @social_link['Image'] }}"></a></repeat>
                    </div>
                    </check>
                    <div id="stats" class="section">
                        <h2 class="section-header">Stats</h2>
                        <p><check if="{{ @current_person->getGender() != '1' }}"><true>She</true><false>He</false></check> has hosted <strong>{{ @host_count }}</strong> episode<check if="{{ @host_count != 1 }}">s</check>, been a guest on <strong>{{ @guest_count }}</strong> episode<check if="{{ @guest_count != 1 }}">s</check>, and sponsored <strong>{{ @sponsor_count }}</strong> episode<check if="{{ @sponsor_count != 1 }}">s</check>.</p>
                    </div>
                </div>
                <div class="clear"></div>
                <check if="{{ count(@recent_videos) > 0 }}">
                <div id="youtube-videos" class="section">
                    <h2 class="section-header">Recent YouTube Videos</h2>
                    <repeat group="{{ @recent_videos }}" value="{{ @video }}">
                    <a href="{{ htmlspecialchars(@video['Link']) }}" class="video">
                        <img alt="{{ @video['Title'] }}" title="{{ @video['Title'] }}" class="video-thumbnail" src="{{ @video['Thumbnail'] }}">
                        <span class="video-title">{{ @video['Title'] }}</span>
                        <div class="video-details">
                            <span class="video-timestamp"><i class="icon-time"></i> {{ @video['Duration'] }}</span>
                            <span class="video-comments"><i class="icon-comments"></i> {{ number_format(@video["Comments"]) }} Comment<check if="@video['Comments'] != 1">s</check></span>
                        </div>
                    </a>
                    </repeat>
                    <div class="clear"></div>
                </div>
                </check>