<check if="{{ @type == 'person' }}">
            <meta property="og:type" content="profile">
            <meta property="og:title" content="{{ @current_person->getName() }}">
            <meta property="og:description" content="{{ @current_person->getOverview() }}">
            <meta property="og:url" content="{{ @domain }}person/{{ @current_person->getID() }}">
            <meta property="fb:profile_id" content="{{ @current_person->getFacebook() }}">
            <meta property="profile:first_name" content="{{ @current_person->getFirstName() }}">
            <meta property="profile:last_name" content="{{ @current_person->getLastName() }}">
            <meta property="profile:username" content="{{ @current_person->getName() }}">
            <meta property="profile:gender" content="<check if='{{ @current_person->getGender() != "1" }}'><true>female</true><false>male</false></check>">
</check>
<check if="{{ @type == 'episode' }}">
    <check if="{{ @source == 'get' }}">
        <true>
        <meta property="og:type" content="music.song">
        <meta property="og:title" content="{{ @Core->getName() }} #{{ @current_episode->getNumber() }}">
        <meta property="og:description" content="Guests: {{ @guests_list }}">
        <meta property="og:url" content="{{ @domain }}episode/{{ @current_episode->getNumber() }}">
        <meta property="music:album" content="{{ @domain }}">
        <meta property="music:album:track" content="{{ @current_episode->getNumber() }}">
        <meta property="music:duration" content="{{ @current_episode->getYouTubeLength() }}">
        <repeat group="{{ @current_episode->getHosts() }}" value="{{ @host }}">
        <meta property="music:musician" content="{{ @domain }}person/{{ @host->getID() }}">
        </repeat>
        <repeat group="{{ @current_episode->getGuests() }}" value="{{ @guest }}">
        <meta property="music:musician" content="{{ @domain }}person/{{ @guest->getID() }}">
        </repeat>
        </true>
        <false>
        <meta property="og:type" content="music.album">
        <meta property="og:title" content="{{ @Core->getName() }}">
        <meta property="og:description" content="{{ @description }}">
        <meta property="og:url" content="{{ @domain }}">
        <meta property="music:release_date" content="2010-04-19">
        <repeat group="{{ @episodes }}" value="{{ @episode }}">
        <meta property="music:song" content="{{ @domain }}episode/{{ @episode->getNumber() }}">
        <meta property="music:song:disc" content="1">
        <meta property="music:song:track" content="{{ @episode->getNumber() }}">
        </repeat>
        </false>
    </check>
        
        <!-- Twitter -->
        <meta property="twitter:site" content="@{{ @twitter }}">
        <meta property="twitter:creator" content="@{{ @creator }}">
        <meta property="twitter:domain" content="{{ @base_domain }}">
    <check if="{{ @source == 'get' }}">
        <true>
        <meta property="twitter:card" content="player">
        <meta property="twitter:title" content="{{ @Core->getName() }} #{{ @current_episode->getNumber() }}">
        <meta property="twitter:description" content="Guests: {{ @guests_list }}">
        <meta property="twitter:image:src" content="http://i1.ytimg.com/vi/{{ @current_episode->getYouTube() }}/hqdefault.jpg">
        <meta property="twitter:player" content="https://www.youtube.com/embed/{{ @current_episode->getYouTube() }}">
        <meta property="twitter:player:height" content="1280">
        <meta property="twitter:player:width" content="720">
        </true>
        <false>
        <meta property="twitter:card" content="summary">
        <meta property="twitter:title" content="{{ @Core->getName() }}">
        <meta property="twitter:description" content="{{ @description }}">
        <meta property="twitter:image:src" content="{{ @domain }}img/pka.png">
        </false>
    </check>
</check>