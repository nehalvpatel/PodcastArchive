<template>
    <li>
        <router-link :to="episodeLink" :class="episodeClass" exact>
            <span v-text="poundEpisodeNumber"></span>
            <span v-if="timelined" class="timelined"></span>
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
        timelined: {
            type: Boolean,
            required: true
        }
    }
}
</script>