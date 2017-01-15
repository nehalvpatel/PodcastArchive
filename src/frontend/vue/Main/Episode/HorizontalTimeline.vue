<template>
    <div :class="[$style.horizontalTimelineSection, $style.section]">
        <h4 :class="$style.sectionHeader">Timeline</h4>
        <div :class="$style.horizontalTimeline">
            <horizontal-timestamp v-for="timestamp in timestamps" :key="timestampKey(timestamp.ID)" :episodeNumber="episodeNumber" :timestamp="timestamp"></horizontal-timestamp>
        </div>
    </div>
</template>

<script>
import HorizontalTimestamp from "./HorizontalTimestamp.vue";

module.exports = {
    components: {
        HorizontalTimestamp
    },
    props: {
        timestamps: {
            type: Array,
            required: true
        },
        episodeNumber: {
            type: Number,
            required: true
        }
    },
    methods: {
        timestampKey: function(id) {
            return "timestamp-h-" + id;
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
    .horizontalTimeline {
        height: 35px;
        -webkit-transition: margin-bottom 0.1s ease-out;
        transition: margin-bottom 0.1s ease-out;
    }
    .horizontalTimeline:hover {
        margin-bottom: 35px;
    }
    @media (max-width: 1024px) {
        /* Horizontal Timeline Styles */
        .horizontalTimelineSection {
            display: none;
        }
    }
</style>