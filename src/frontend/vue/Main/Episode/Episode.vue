<template>
    <div>
        <h2 v-text="episodeTitle"></h2>
        <div>
            <span title="Date Published">
                <i :class="$style.infoIcon" class="icon-time"></i><small :class="$style.infoText"><time :datetime="episode.DateTime" v-text="episode.Date"></time></small>
            </span>
            <a v-if="hasReddit" :class="$style.optionalInfo" title="Discussion Comments" :href="redditCommentsLink">
                <i :class="$style.infoIcon" class="icon-comments"></i><small :class="$style.infoText" v-text="redditCommentCount"></small>
            </a>
            <a v-if="hasAuthor" :class="$style.optionalInfo" title="Timeline Author" :href="episode.Timeline.Author.Link">
                <i :class="$style.infoIcon" class="icon-user"></i><small :class="$style.infoText" v-text="episode.Timeline.Author.Name"></small>
            </a>
        </div>
        <div :class="$style.videoClear"></div>
        <div>
            <youtube :videoId="videoId" playerHeight="400px" playerWidth="100%" :playerVars="videoArgs" @ready="playerReady" @playing="playerPlaying" @ended="playerEnded" @paused="playerIdle" @buffering="playerIdle" @cued="playerCued" @error="playerIdle"></youtube>
        </div>
        <div v-if="hasHosts" :class="[$style.items, $style.section]">
            <h4 :class="$style.sectionHeader">Hosts</h4>
            <person-item v-for="person in episode.People.Hosts" :key="personKey(person.ID)" :person="person"></person-item>
        </div>
        <div v-if="hasGuests" :class="[$style.items, $style.section]">
            <h4 :class="$style.sectionHeader">Guests</h4>
            <person-item v-for="person in episode.People.Guests" :key="personKey(person.ID)" :person="person"></person-item>
        </div>
        <div v-if="hasSponsors" :class="[$style.items, $style.section]">
            <h4 :class="$style.sectionHeader">Sponsors</h4>
            <person-item v-for="person in episode.People.Sponsors" :key="personKey(person.ID)" :person="person"></person-item>
        </div>
        <div :class="$style.timelineClear"></div>
        <horizontal-timeline v-if="hasTimestamps" :timestamps="episode.Timeline.Timestamps" :episodeNumber="episode.Number"></horizontal-timeline>
        <div v-if="this.$store.state.loggedIn" :class="$style.section">
            <h4 :class="$style.sectionHeader">Add Single Timeline Row</h4>
            <form @submit.prevent="addTimestamp" method="POST">
                <input type="text" v-model="formAddTime" :class="$style.timestampTimeField" placeholder="1:23:45" />
                <input type="text" v-model="formAddEvent" :class="$style.timestampEventField" placeholder="The hosts talk about a topic" />
                <input type="text" v-model="formAddURL" :class="$style.timestampURLField" placeholder="http://www.relevanturl.com (optional)" />
                <button type="submit" :class="$style.timestampSubmitButton">Add Timeline Row</button>
            </form>
        </div>
        <vertical-timeline v-if="hasTimestamps" :timestamps="episode.Timeline.Timestamps" :episodeNumber="episode.Number"></vertical-timeline>
        <div v-if="this.$store.state.loggedIn" :class="$style.section">
            <h4 :class="$style.sectionHeader">Add Timeline</h4>
            <form @submit.prevent="addTimeline" method="POST">
                <textarea v-model.trim="formAddTimeline" :class="$style.timelineTextarea" :placeholder="formAddTimelinePlaceholder"></textarea>
                <button type="submit" :class="$style.timelineSubmitButton">Submit Timeline</button>
            </form>
        </div>
    </div>
</template>

<script>
module.exports = {
    metaInfo: function() {
        var title = "Painkiller Already";
        var titleTemplate = null;
        var description = "Commonly referred to as PKA, the podcast discusses current events, news, relives comedic stories and gives their perspective on life while throwing in their comedic twist to all discussions.";

        if (this.$route.name === "specific-episode") {
            title = "Episode #" + this.episode.Number;
            titleTemplate = "%s · Painkiller Already"
            description = this.episode.Description;
        }

        return {
            title: title,
            titleTemplate: titleTemplate,
            meta: [
                { vmid: "description", name: "description", content: description }
            ]
        };
    },
    data: function() {
        var videoArgs = {
            autoplay: 1
        }

        if (this.$route.query.timestamp) {
            videoArgs["start"] = this.$route.query.timestamp;
        } else {
            if (localStorage.getItem(this.$store.state.episodeIdentifier)) {
                videoArgs["start"] = parseInt(localStorage.getItem(this.$store.state.episodeIdentifier));
            } else {
                videoArgs["start"] = 0;
            }
        }

        return {
            formAddTime: "",
            formAddEvent: "",
            formAddURL: "",
            formAddTimeline: "",
            formAddTimelinePlaceholder: "23:45 The hosts talk about a topic\r\n1:32:54 The hosts talk about a topic with a relevant website http://www.relevanturl.com",
            videoId: this.$store.state.episodes[this.$store.state.episodeIdentifier].YouTube,
            videoTimer: null,
            videoTime: 0,
            videoArgs: videoArgs
        };
    },
    computed: {
        episode: function() {
            return this.$store.state.episodes[this.$store.state.episodeIdentifier];
        },
        episodeTitle: function() {
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
        playerReady: function(player) {
            this.$store.commit("mountPlayer", player);
        },
        playerPlaying: function() {
            this.videoTimer = setInterval(this.updateTime, 1000);
        },
        playerCued: function() {
            this.$store.state.episodePlayer.playVideo();
        },
        playerEnded: function() {
            localStorage.removeItem(this.$store.state.episodeIdentifier);
            
            this.playerIdle();
        },
        playerIdle: function() {
            clearTimeout(this.videoTimer);
        },
        updateTime: function() {
            var oldTime = this.videoTime;
            if (this.$store.state.episodePlayer && this.$store.state.episodePlayer.getCurrentTime) {
                this.videoTime = this.$store.state.episodePlayer.getCurrentTime();
            }

            if (this.videoTime !== oldTime) {
                this.onProgress(this.videoTime);
            }
        },
        onProgress: function(currentTime) {
            this.$store.dispatch("setEpisodeProgress", {
                Identifier: this.episode.Identifier,
                Timestamp: currentTime
            });

            for (var i = 0; i < this.episode.Timeline.Timestamps.length; i++) {
                var currentTimestamp = this.episode.Timeline.Timestamps[i];
                
                if ((currentTime > currentTimestamp.Begin) && (currentTime < currentTimestamp.End)) {
                    this.$store.dispatch("highlightTimestamp", {
                        TimestampIndex: currentTimestamp.ID
                    });
                }
            }
        },
        addTimestamp: function() {
            if (this.formAddTime && this.formAddEvent) {
                this.$store.dispatch("addTimestamp", {
                    Identifier: this.episode.Identifier,
                    formAddTimestamp: this.formAddTime,
                    formAddEvent: this.formAddEvent,
                    formAddURL: this.formAddURL
                }).then((messages) => {
                    this.$store.dispatch("displaySuccesses", messages);

                    this.formAddTime = "";
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
        },
        loadNextVideo: function(videoId, startSeconds) {
            var name = "cueVideoById";

            if (this.$store.state.episodePlayer && this.$store.state.episodePlayer.hasOwnProperty(name)) {
                this.$store.state.episodePlayer[name]({
                    videoId: videoId,
                    startSeconds: startSeconds
                });
            } else {
                setTimeout(() => {
                    this.loadNextVideo(videoId, startSeconds);
                }, 100);
            }
        }
    },
    watch: {
        $route: function(to, from) {
            this.$store.dispatch("handleEpisodeNavigation", to);
        },
        "episode.YouTube": function(to, from) {
            if (this.$route.query.timestamp) {
                this.loadNextVideo(to, this.$route.query.timestamp);
            } else {
                if (localStorage.getItem(this.$store.state.episodeIdentifier)) {
                    this.loadNextVideo(to, parseInt(localStorage.getItem(this.$store.state.episodeIdentifier)));
                } else {
                    this.loadNextVideo(to);
                }
            }
        }
    }
}
</script>

<style module>
    .section {
        composes: section from "./../../Global.css"
    }
    .sectionHeader {
        composes: sectionHeader from "./../../Global.css"
    }
    .items {
        composes: items from "./../../Global.css"
    }
    .videoClear {
        composes: clear from "./../../Global.css";
        height: 20px;
    }
    .timelineClear {
        composes: clear from "./../../Global.css"
    }
    .infoIcon {
        font-size: 15px;
    }
    .infoText {
        margin-left: 5px;
    }

    .optionalInfo {
        text-decoration: none;
        color: inherit;
        margin-left: 10px;
    }

    .timestampFormField {
        padding: 5px;
    }

    .timestampTimeField {
        composes: timestampFormField;
        width: 10%;
        min-width: 60px;
    }

    .timestampEventField {
        composes: timestampFormField;
        width: 30%;
    }

    .timestampURLField {
        composes: timestampFormField;
        width: 30%;
    }

    .timestampSubmitButton {
        padding: 7px;
    }

    .timelineTextarea {
        composes: textarea from "./../../Global.css"
    }

    .timelineSubmitButton {
        composes: textareaSubmitButton from "./../../Global.css"
    }

    @media (max-width: 1024px) {
        .optionalInfo {
            float: right;
        }
    }
</style>