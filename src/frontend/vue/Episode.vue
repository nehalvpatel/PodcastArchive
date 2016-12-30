<template>
    <div class="eps">
        <h2 v-text="episodeTitle"></h2>
        <div class="info">
            <span class="published" title="Date Published">
                <i class="icon-time"></i><small><time :datetime="episode.DateTime" v-text="episode.Date"></time></small>
            </span>
            <a v-if="episode.Reddit != ''" class="comments" title="Discussion Comments" :href="redditCommentsLink">
                <i class="icon-comments"></i><small id="comments" :data-reddit="episode.Reddit" v-text="redditCommentCount"></small>
            </a>
            <a v-if="hasAuthor" class="author" title="Timeline Author" :href="episode.Timeline.Author.Link">
                <i class="icon-user"></i><small v-text="episode.Timeline.Author.Name"></small>
            </a>
        </div>
        <div id="rock-hardplace" class="clear"></div>
        <div id="video">
            <youtube :videoId="episode.YouTube" playerHeight="400px" playerWidth="100%" :playerVars="videoArgs" v-on:ready="playerReady" v-on:playing="playerPlaying" v-on:ended="playerIdle" v-on:paused="playerIdle" v-on:buffering="playerIdle" v-on:qued="playerIdle" v-on:error="playerIdle"></youtube>
        </div>
        <div id="Hosts" class="section items">
            <h4 class="section-header">Hosts</h4>
            <person-item v-for="(person, index) in episode.People.Hosts" :person="person"></person-item>
        </div>
        <div v-if="episode.People.Guests" id="guests" class="section items">
            <h4 class="section-header">Guests</h4>
            <person-item v-for="(person, index) in episode.People.Guests" :person="person"></person-item>
        </div>
        <div v-if="episode.People.Sponsors" id="sponsors" class="section items">
            <h4 class="section-header">Sponsors</h4>
            <person-item v-for="(person, index) in episode.People.Sponsors" :person="person"></person-item>
        </div>
        <div id="timeline-clear" class="clear"></div>
        <div v-if="hasTimestamps" id="timeline-horizontal" class="section">
            <h4 class="section-header">Timeline</h4>
            <div class="timeline">
                <horizontal-timestamp v-for="(timestamp, index) in episode.Timeline.Timestamps" :number="episode.Number" :timestamp="timestamp" v-on:seek="seekTo"></horizontal-timestamp>
            </div>
        </div>
        <table v-if="hasTimestamps" id="timeline-vertical" class="section">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Event</th>
                </tr>
            </thead>
            <tbody>
                <vertical-timestamp v-for="(timestamp, index) in episode.Timeline.Timestamps" :number="episode.Number" :timestamp="timestamp" v-on:seek="seekTo"></vertical-timestamp>
            </tbody>
        </table>
    </div>
</template>

<script>
module.exports = {
    data: function() {
        return {
            episode: this.handleNavigation(true),
            videoPlayer: null,
            videoTimer: null,
            videoTime: 0,
            videoArgs: {
				start: this.$route.query.timestamp,
				autoplay: 0
			}
        };
    },
    computed: {
        episodeTitle: function() {
            document.title = "Episode #" + this.episode.Number + " \u00B7 Painkiller Already";
            return "Painkiller Already #" + this.episode.Number;
        },
        redditCommentsLink: function() {
            if (this.episode.RedditLink) {
                return this.episode.RedditLink;
            } else {
                return "https://www.reddit.com/comments/" + this.episode.Reddit;
            }
        },
        redditCommentCount: function() {
            if (this.episode.RedditCount > 0) {
                return this.episode.RedditCount + " Comments";
            } else {
                return "Comments";
            }
        },
        hasAuthor: function() {
            if (this.episode.Timeline.Author) {
                if (this.episode.Timeline.Author.Name) {
                    return true;
                }
            }

            return false;
        },
        hasTimestamps: function() {
            return Object.keys(this.episode.Timeline.Timestamps).length > 0;
        }
    },
    watch: {
        $route: function() {
            this.episode = this.handleNavigation(false);
        }
    },
    methods: {
        seekTo: function(timestamp) {
            this.videoPlayer.seekTo(timestamp);
            document.getElementsByTagName("header")[0].scrollIntoView();
        },
        playerReady: function(player) {
            this.videoPlayer = player;
        },
        playerPlaying: function() {
            this.videoTimer = setInterval(this.updateTime, 1000);
        },
        playerIdle: function() {
            clearTimeout(this.videoTimer);
        },
        updateTime: function() {
            var oldTime = this.videoTime;
            if (this.videoPlayer && this.videoPlayer.getCurrentTime) {
                this.videoTime = this.videoPlayer.getCurrentTime();
            }

            if (this.videoTime !== oldTime) {
                this.onProgress(this.videoTime);
            }
        },
        onProgress: function(currentTime) {
            for (var key in this.episode.Timeline.Timestamps) {
                // skip loop if the property is from prototype
                if (!this.episode.Timeline.Timestamps.hasOwnProperty(key)) continue;

                var currentTimestamp = this.episode.Timeline.Timestamps[key];

                if ((currentTime > currentTimestamp.Begin) && (currentTime < currentTimestamp.End)) {
                    this.$set(this.episode.Timeline.Timestamps[key], "Highlighted", true);
                } else {
                    this.$set(this.episode.Timeline.Timestamps[key], "Highlighted", false);
                }
            }
        },
        handleNavigation: function(firstLaunch) {
            var episodeObject = {};
            if (this.$route.name == "latest-episode") {
                episodeObject = this.$store.state.episodes[this.$store.state.latest.Identifier];
            } else if (this.$route.name == "random-episode") {
                var keys = Object.keys(this.$store.state.episodes);
                var random = keys[ keys.length * Math.random() << 0];

                episodeObject = this.$store.state.episodes[random];

                this.$router.replace("/episode/" + episodeObject.Number);
            } else if (this.$route.name == "specific-episode") {
                episodeObject = this.$store.state.episodes[this.$store.state.map[this.$route.params.number]];

                if (!episodeObject.Loaded) {
                    this.fetchEpisode(this.$route.params.number, firstLaunch);
                } else {
                    this.$store.commit("closeSidebar");
                }
            }

            return episodeObject;
        },
        fetchEpisode: function(number, firstLaunch) {
            fetch("http://localhost:8080/api/json/" + number + ".json")
                .then((response) => {
                    return response.json();
                }).then((json) => {
                    if (this.episode.Number === number) {
                        this.episode = json;

                        this.$store.commit("closeSidebar");

                        if (firstLaunch) {
                            document.querySelector(".router-link-active").scrollIntoView();
                        }

                        if (json.Reddit) {
                            this.fetchRedditCount(number, json.Reddit);
                        } else {
                            this.$store.commit("cacheEpisode", this.episode);
                        }
                    }
                });
        },
        fetchRedditCount: function(number, Reddit) {
            fetch("https://www.reddit.com/comments/" + Reddit + ".json")
                .then((response) => {
                    return response.json();
                }).then((json) => {
                    if (this.episode.Number === number) {
                        this.$set(this.episode, "RedditCount", json[0].data.children[0].data.num_comments);
                        this.$set(this.episode, "RedditLink", "https://www.reddit.com" + json[0].data.children[0].data.permalink);

                        this.$store.commit("cacheEpisode", this.episode);
                    }
                });
        }
    }
}
</script>