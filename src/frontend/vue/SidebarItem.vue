<template>
    <li v-if="showLink">
        <router-link :to="episodeLink" :class="episodeClass" exact>
            <span>
                <span v-text="poundEpisodeNumber"></span>
                <span v-if="timelined" class="timelined"></span>
            </span>
        </router-link>
        <search-result v-if="searchResults.length > 0" v-for="result in searchResults" :episodeNumber="number" :result="result"></search-result>
    </li>
</template>

<script>
module.exports = {
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
                "highlighted-episode": this.highlighted
            };
        },
        poundEpisodeNumber: function() {
            return "#" + this.number;
        },
        showLink: function() {
            if (this.$store.state.searchMode) {
                if (this.searchResults.length === 0) {
                    return false;
                }
            }

            return true;
        }
    },
    props: {
        number: {
            type: Number,
            required: true
        },
        highlighted: {
            type: Boolean,
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