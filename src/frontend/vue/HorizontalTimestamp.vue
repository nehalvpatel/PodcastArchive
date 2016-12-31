<template>
    <a class="timelink" :href="timestampLink" @click.prevent="seek">
        <div class="topic" :class="timestampClass" :style="timestampWidth">
            <div class="tooltip" :class="tooltipAlignment" :id="timestamp.ID">
                <div class="triangle"></div>
                <span v-text="timestamp.Value"></span>
            </div>
        </div>
    </a>
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
        tooltipAlignment: function() {
            return {
                right: this.timestamp.Right
            };
        },
        timestampWidth: function() {
            return {
                width: this.timestamp.Width
            };
        },
        timestampClass: function() {
            return {
                "active-timestamp-horizontal": this.timestamp.Highlighted
            };
        },
        timestampLink: function() {
            return "/episode/" + this.episodeNumber + "?timestamp=" + this.timestamp.Begin;
        }
    }
};
</script>