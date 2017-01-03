<template>
    <div class="episode">
        <div v-if="$store.state.successes" v-for="success in $store.state.successes" class="success-message">
            <p v-text="success"></p>
        </div>
        <div v-if="$store.state.errors" v-for="error in $store.state" class="error-message">
            <p v-text="error"></p>
        </div>
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
        <div v-if="hasHosts" id="Hosts" class="section items">
            <h4 class="section-header">Hosts</h4>
            <person-item v-for="person in episode.People.Hosts" :key="personKey(person.ID)" :person="person"></person-item>
        </div>
        <div v-if="hasGuests" id="guests" class="section items">
            <h4 class="section-header">Guests</h4>
            <person-item v-for="person in episode.People.Guests" :key="personKey(person.ID)" :person="person"></person-item>
        </div>
        <div v-if="hasSponsors" id="sponsors" class="section items">
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
        <div v-if="this.$store.state.loggedIn" id="addTimelineRow" class="section">
            <h4 class="section-header">Add Single Timeline Row</h4>
            <form @submit.prevent="addTimelineRow" method="POST">
                <input type="text" v-model="formAddTimestamp" id="time" placeholder="1:23:45" />
                <input type="text" v-model="formAddEvent" id="event" placeholder="The hosts talk about a topic" />
                <input type="text" v-model="formAddURL" id="url" placeholder="http://www.relevanturl.com (optional)" />
                <input type="submit" value="Add Timeline Row" />
            </form>
        </div>
        <table v-if="hasTimestamps" id="timeline-vertical" class="section">
            <thead>
                <tr>
                    <th v-if="this.$store.state.loggedIn">Delete</th>
                    <th>Time</th>
                    <th>Event</th>
                </tr>
            </thead>
            <tbody>
                <vertical-timestamp v-for="timestamp in episode.Timeline.Timestamps" :key="timestampKey(timestamp.ID)" :episodeNumber="episode.Number" :timestamp="timestamp" @seek="seekTo"></vertical-timestamp>
            </tbody>
        </table>
        <div v-if="this.$store.state.loggedIn" id="Add Timeline" class="section">
            <h4 class="section-header">Add Timeline</h4>
            <form @submit.prevent="addTimeline" method="POST">
                <textarea v-model="formAddTimeline" :placeholder="formAddTimelinePlaceholder"></textarea>
                <input type="submit" value="Submit Timeline" />
            </form>
        </div>
    </div>
</template>

<script>
module.exports = {
    data: function() {
        return {
            formAddTimestamp: "",
            formAddEvent: "",
            formAddURL: "",
            formAddTimeline: "",
            formAddTimelinePlaceholder: "23:45 The hosts talk about a topic\r\n1:32:54 The hosts talk about a topic with a relevant website http://www.relevanturl.com",
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
        episode: function() {
            return this.$store.state.episodes[this.$store.state.episodeIdentifier];
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
        hasHosts: function() {
            if (this.episode.People && this.episode.People.Hosts) {
                return true;
            } else {
                return false;
            }
        },
        hasGuests: function() {
            if (this.episode.People && this.episode.People.Guests) {
                return true;
            } else {
                return false;
            }
        },
        hasSponsors: function() {
            if (this.episode.People && this.episode.People.Sponsors) {
                return true;
            } else {
                return false;
            }
        },
        hasTimestamps: function() {
            return this.episode.Timelined;
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
                
                var timestampAction = "unhighlightTimestamp";
                if ((currentTime > currentTimestamp.Begin) && (currentTime < currentTimestamp.End)) {
                    timestampAction = "highlightTimestamp";
                }

                this.$store.dispatch(timestampAction, {
                    Identifier: this.episode.Identifier,
                    Highlighted: currentTimestamp.Highlighted,
                    TimestampIndex: i
                });
            }
        },
        addTimelineRow: function() {
            if (this.formAddTimestamp && this.formAddEvent && this.formAddURL) {
                this.$store.dispatch("addTimestamp", {
                    Identifier: this.episode.Identifier,
                    formAddTimestamp: this.formAddTimestamp,
                    formAddEvent: this.formAddEvent,
                    formAddURL: this.formAddURL
                }).then((messages) => {
                    this.$store.dispatch("displaySuccesses", messages);

                    this.formAddTimestamp = "";
                    this.formAddEvent = "";
                    this.formAddURL = "";
                }).catch((messages) => {
                    this.$store.dispatch("displayErrors", messages);
                });
            } else {
                this.$store.dispatch("displayErrors", ["Please make sure you filled in the timestamp row."]);
            }
        },
        addTimeline: function() {
            if (this.formAddTimeline) {
                this.$store.dispatch("addTimeline", {
                    Identifier: this.episode.Identifier,
                    formAddTimeline: this.formAddTimeline,
                }).then((messages) => {
                    this.$store.dispatch("displaySuccesses", messages);

                    this.formAddTimeline = "";
                }).catch((messages) => {
                    this.$store.dispatch("displayErrors", messages);
                });
            } else {
                this.$store.dispatch("displayErrors", ["Please make sure you filled in the timeline."]);
            }
        }
    },
    watch: {
        $route: function(to, from) {
            this.$store.dispatch("handleEpisodeNavigation", to);
        }
    }
}
</script>