<template>
    <tr :class="timestampClass">
        <td class="timestamp"><a class="timelink" :href="timestampLink" @click.prevent="seek" v-text="timestamp.HMS"></a></td>
        <td class="event">
            <a v-if="timestamp.URL" target="_blank" :href="timestamp.URL" v-text="timestamp.Value"></a>
            <span v-else v-text="timestamp.Value"></span>
        </td>
    </tr>
</template>

<script>
module.exports = {
    props: {
        timestamp: {
            type: Object,
            required: true
        },
        episodeNumber: {
            type: Number,
            required: true
        }
    },
    methods: {
        seek: function() {
            this.$emit("seek", this.episodeNumber, this.timestamp);
        }
    },
    computed: {
        timestampClass: function() {
            return {
                "active-timestamp-vertical": this.timestamp.Highlighted
            };
        },
        timestampLink: function() {
            return "/episode/" + this.episodeNumber + "?timestamp=" + this.timestamp.Begin;
        }
    }
};
</script>