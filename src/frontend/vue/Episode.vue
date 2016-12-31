<template>
    <div class="episode">
        <h2 v-text="episodeTitle"></h2>
        <div class="info">
            <span class="published" title="Date Published">
                <i class="icon-time"></i><small><time :datetime="episode.DateTime" v-text="episode.Date"></time></small>
            </span>
            <a v-if="hasReddit" class="comments" title="Discussion Comments" :href="redditCommentsLink">
                <i class="icon-comments"></i><small id="comments" v-text="redditCommentCount"></small>
            </a>
            <a v-if="hasAuthor" class="author" title="Timeline Author" :href="episode.Timeline.Author.Link">
                <i class="icon-user"></i><small v-text="episode.Timeline.Author.Name"></small>
            </a>
        </div>
        <div id="rock-hardplace" class="clear"></div>
        <div id="video">
            <youtube :videoId="episode.YouTube" playerHeight="400px" playerWidth="100%" :playerVars="videoArgs" @ready="playerReady" @playing="playerPlaying" @ended="playerIdle" @paused="playerIdle" @buffering="playerIdle" @qued="playerIdle" @error="playerIdle"></youtube>
        </div>
        <div id="Hosts" class="section items">
            <h4 class="section-header">Hosts</h4>
            <person-item v-for="person in episode.People.Hosts" :key="personKey(person.ID)" :person="person"></person-item>
        </div>
        <div v-if="episode.People.Guests" id="guests" class="section items">
            <h4 class="section-header">Guests</h4>
            <person-item v-for="person in episode.People.Guests" :key="personKey(person.ID)" :person="person"></person-item>
        </div>
        <div v-if="episode.People.Sponsors" id="sponsors" class="section items">
            <h4 class="section-header">Sponsors</h4>
            <person-item v-for="person in episode.People.Sponsors" :key="personKey(person.ID)" :person="person"></person-item>
        </div>
        <div id="timeline-clear" class="clear"></div>
        <div v-if="hasTimestamps" id="timeline-horizontal" class="section">
            <h4 class="section-header">Timeline</h4>
            <div class="timeline">
                <horizontal-timestamp v-for="timestamp in episode.Timeline.Timestamps" :key="timestampKey(timestamp.ID)" :episodeNumber="episode.Number" :timestamp="timestamp" @seek="seekTo"></horizontal-timestamp>
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
                <vertical-timestamp v-for="timestamp in episode.Timeline.Timestamps" :key="timestampKey(timestamp.ID)" :episodeNumber="episode.Number" :timestamp="timestamp" @seek="seekTo"></vertical-timestamp>
            </tbody>
        </table>
    </div>
</template>

<script>
module.exports = {
    data: function() {
        return {
            identifier: this.handleNavigation(true),
            videoPlayer: null,
            videoTimer: null,
            videoTime: 0,
            videoArgs: {
				start: this.$route.query.timestamp,
				autoplay: 1
			}
        };
    },
    computed: {
        episode: function() {
            return this.$store.state.episodes[this.identifier];
        },
        episodeTitle: function() {
            document.title = "Episode #" + this.episode.Number + " \u00B7 Painkiller Already";
            return "Painkiller Already #" + this.episode.Number;
        },
        hasReddit: function() {
            if (this.episode.Reddit) {
                return true;
            } else {
                return false;
            }
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
            return this.episode.Timelined;
        }
    },
    watch: {
        $route: function() {
            this.identifier = this.handleNavigation(false);
        }
    },
    methods: {
        personKey: function(id) {
            return "person-" + id;
        },
        timestampKey: function(id) {
            return "timestamp-" + id;
        },
        seekTo: function(episodeNumber, timestamp) {
            if (this.videoPlayer) {
                this.$router.replace({
                    name: "specific-episode",
                    params: {
                        number: episodeNumber
                    },
                    query: {
                        timestamp: timestamp.Begin
                    }
                });

                this.videoPlayer.seekTo(timestamp.Begin);

                document.getElementsByTagName("header")[0].scrollIntoView();
            }
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
            for (var i = 0; i < this.episode.Timeline.Timestamps.length; i++) {
                var currentTimestamp = this.episode.Timeline.Timestamps[i];

                if ((currentTime > currentTimestamp.Begin) && (currentTime < currentTimestamp.End)) {
                    if (currentTimestamp.Highlighted === false) {
                        this.$store.commit("highlightTimestamp", {
                            Identifier: this.episode.Identifier,
                            TimestampIndex: i
                        });
                    }
                } else {
                    if (currentTimestamp.Highlighted === true) {
                        this.$store.commit("unhighlightTimestamp", {
                            Identifier: this.episode.Identifier,
                            TimestampIndex: i
                        });
                    }
                }
            }
        },
        handleNavigation: function(firstLaunch) {
            var episodeIdentifier = null;
            if (this.$route.name === "latest-episode") {
                episodeIdentifier = this.$store.state.latest.Identifier;

                this.$store.dispatch("fetchEpisode", {
                    episodeToFetch: this.$store.state.latest.Number,
                    firstLaunch: firstLaunch
                });
            } else if (this.$route.name === "random-episode") {
                var episodeKeys = Object.keys(this.$store.state.episodes);
                episodeIdentifier = episodeKeys[episodeKeys.length * Math.random() << 0];

                this.$router.replace("/episode/" + this.$store.state.episodes[episodeIdentifier].Number);
            } else if (this.$route.name === "specific-episode") {
                episodeIdentifier = this.$store.state.map[this.$route.params.number];

                if (this.$store.state.episodes[episodeIdentifier].Loaded) {
                    this.$store.commit("closeSidebar");
                } else {
                    this.$store.dispatch("fetchEpisode", {
                        episodeToFetch: this.$route.params.number,
                        firstLaunch: firstLaunch
                    });
                }
            }

            this.$store.dispatch("clearAllHighlighted", episodeIdentifier);

            return episodeIdentifier;
        }
    }
}
</script>