<template>
    <a :href="timestampLink" :class="searchResultClass" @click.prevent="seek">
        <span :class="$style.searchResult" :title="result.Value">
            <strong v-text="result.HMS"></strong> - <span v-text="result.Value"></span>
        </span>
    </a>
</template>

<script>
module.exports = {
    computed: {
        timestampLink: function() {
            return "/episode/" + this.episodeNumber + "?timestamp=" + this.result.Timestamp;
        },
        searchResultClass: function() {
            return {
                [this.$style.resultLink]: true,
                [this.$style.searchResultHighlighted]: (this.$store.state.highlightedTimestamp === this.result.ID)
            }
        }
    },
    props: {
        episodeNumber: {
            type: Number,
            required: true
        },
        result: {
            type: Object,
            required: true
        }
    },
    methods: {
        seek: function() {
            if (this.$store.state.episodeIdentifier === this.$store.state.map[this.episodeNumber]) {
                this.$store.state.episodePlayer.seekTo(this.result.Timestamp);

                this.$router.replace({
                    name: "specific-episode",
                    params: {
                        number: this.episodeNumber
                    },
                    query: {
                        timestamp: this.result.Timestamp
                    }
                });
            } else {
                this.$router.push({
                    name: "specific-episode",
                    params: {
                        number: this.episodeNumber
                    },
                    query: {
                        timestamp: this.result.Timestamp
                    }
                });
            }
        }
    }
}
</script>

<style module>
.searchResult {
	width: 175px;
	display: block;
	font-size: smaller;
	font-weight: normal;
}
.searchResultHighlighted {
    background: #9c463a;
    color: #FFFFFF;
}
.resultLink {
    composes: sidebarLink from "./../Global.css";
}
</style>