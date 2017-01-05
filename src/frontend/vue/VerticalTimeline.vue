<template>
    <table :class="[$style.verticalTimelineSection, $style.section]">
        <thead :class="$style.verticalTimelineHead">
            <tr>
                <th v-if="this.$store.state.loggedIn">Delete</th>
                <th>Time</th>
                <th>Event</th>
            </tr>
        </thead>
        <tbody :class="$style.verticalTimelineBody">
            <vertical-timestamp v-for="timestamp in timestamps" :key="timestampKey(timestamp.ID)" :episodeNumber="episodeNumber" :timestamp="timestamp" @seek="seek"></vertical-timestamp>
        </tbody>
    </table>
</template>

<script>
module.exports = {
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
            return "timestamp-v-" + id;
        },
        seek: function(timestamp) {
            this.$emit("seek", this.episodeNumber, timestamp);
        }
    }
}
</script>

<style module>
.section {
    composes: section from "./Global.css"
}
.verticalTimelineSection {
	width: 100%;
	padding: 0;
	border-collapse: collapse;
	border-spacing: 0;
}
.verticalTimelineHead {
	background: #BB4D3E;
}
.verticalTimelineBody {
	background: #404040;
	font-size: 10.5pt;
}
.verticalTimelineSection tr {
	border-left: 4px solid #BB4D3E;
	border-right: 4px solid #BB4D3E;
}
.verticalTimelineSection th:first-child {
	border-color: #BB4D3E;
}
.verticalTimelineSection th {
	padding: 5px;
}
.verticalTimelineSection td {
	border: 4px solid #BB4D3E;
	padding: 5px 7px;
	vertical-align: top;
}
</style>