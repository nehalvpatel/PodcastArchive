<template>
    <a :href="timestampLink" :class="$style.resultLink">
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
.resultLink {
    composes: sidebarLink from "./../Global.css";
}
</style>