<template>
    <li>
        <router-link :to="episodeLink" :class="episodeClass" exact>
            <span v-text="poundEpisodeNumber"></span>
            <span v-if="episode.Timelined" class="timelined"></span>
        </router-link>
    </li>
</template>

<script>
module.exports = {
    computed: {
        episodeLink: function() {
            return {
                name: "specific-episode",
                params: {
                    number: this.episode.Number
                }
            };
        },
        episodeClass: function() {
            return {
                "highlighted-episode": this.episode.Highlighted
            };
        },
        poundEpisodeNumber: function() {
            return "#" + this.episode.Number;
        }
    },
    props: {
        episode: {
            type: Object,
            required: true
        },
        index: {
            type: String,
            required: true
        }
    }
}
</script>