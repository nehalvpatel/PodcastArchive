<template>
    <li v-if="showLink" :class="$style.sidebarItem">
        <router-link :to="episodeLink" :class="episodeClass" exact>
            <span>
                <span v-text="poundEpisodeNumber"></span>
                <span v-if="timelined" :class="timelinedClass"></span>
            </span>
        </router-link>
        <search-result v-if="searchResults.length > 0" v-for="result in searchResults" :episodeNumber="number" :result="result"></search-result>
    </li>
</template>

<script>
import SearchResult from "./SearchResult.vue";

module.exports = {
    components: {
        SearchResult
    },
    computed: {
        episodeLink: function() {
            return {
                name: "specific-episode",
                params: {
                    number: this.number
                }
            };
        },
        episodeClass: function() {
            return {
                [this.$style.sidebarItemLink]: true,
                [this.$style.sidebarItemActiveLink]: (this.$store.state.episodeIdentifier === this.identifier) && this.$route.name === "specific-episode",
                [this.$style.sidebarItemHighlighted]: this.highlighted
            };
        },
        timelinedClass: function() {
            return {
                [this.$style.sidebarItemActiveTimelined]: (this.$store.state.episodeIdentifier === this.identifier) && this.$route.name === "specific-episode",
                [this.$style.sidebarItemTimelined]: true
            };
        },
        poundEpisodeNumber: function() {
            return "#" + this.number;
        },
        showLink: function() {
            if (this.$store.state.searchMode && this.searchResults.length === 0) {
                return false;
            }

            return true;
        },
        highlighted: function() {
            if (this.$route.name === "specific-person") {
                if (this.$store.state.people[this.$route.params.number].Loaded) {
                    if (this.$store.state.people[this.$route.params.number].Episodes.indexOf(this.identifier) != -1) {
                        return true;
                    }
                }
            }

            return false;
        }
    },
    props: {
        number: {
            type: Number,
            required: true
        },
        identifier: {
            type: String,
            required: true
        },
        searchResults: {
            type: Array,
            required: true
        },
        timelined: {
            type: Boolean,
            required: true
        }
    }
}
</script>

<style module>
.sidebarItem {
    background: #333;
	list-style: none;
	border-bottom: 1px solid #444;
	color: #aaa;
}
.sidebarItemLink {
    composes: sidebarLink from "./../Global.css";
}
.sidebarItemTimelined {
    composes: label from "./../Global.css";
}
.sidebarItemTimelined:before {
    content: "TIMELINED";
}
.sidebarItemHighlighted {
	border-left: 10px solid #444;
}
.sidebarItemActiveLink {
	background: #BB4D3E;
	color: #FFFFFF;
}
.sidebarItemActiveTimelined {
	background: #A0493E;
	color: #aaa;
}
</style>