<template>
    <a :class="$style.timestampSeekLink" :href="timestampLink" @click.prevent="seek">
        <div :class="timestampClass" :style="timestampWidth">
            <div :class="tooltipAlignment">
                <div :class="$style.timestampEventTooltipTriangle"></div>
                <span :class="$style.timestampEventTooltipText" v-text="timestamp.Value"></span>
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
            this.$emit("seek", this.timestamp);
        }
    },
    computed: {
        tooltipAlignment: function() {
            return {
                [this.$style.timestampEventTooltip]: true,
                [this.$style.timestampEventTooltipRight]: this.timestamp.Right
            };
        },
        timestampWidth: function() {
            return {
                width: this.timestamp.Width
            };
        },
        timestampClass: function() {
            return {
                [this.$style.timestampEvent]: true,
                [this.$style.timestampActive]: (this.$store.state.highlightedTimestamp === this.timestamp.ID)
            };
        },
        timestampLink: function() {
            return "/episode/" + this.episodeNumber + "?timestamp=" + this.timestamp.Begin;
        }
    }
};
</script>

<style module>
.timestampActive {
    background: #414040 !important;
}
.timestampSeekLink {
	color: inherit;
}
.timestampEvent {
	height: 100%;
	float: left;
	background: #333;
	border-right: 1px solid #414040;
	border-top: 4px solid #414040;
}
.timestampEvent:hover {
	background: #414040;
}
.timestampEvent:hover * {
	display: block;
}
.timestampEventTooltip {
	width: 300px;
	padding: 3px;
	background: black;
	position: relative;
	top: 40px;
	text-align: center;
	left: -5px;
	display: none;
}
.timestampEventTooltip .timestampEventTooltipTriangle {
	position: absolute;
	top: -10px;
	height: 0;
	width: 1px;
	border: 5px solid black;
	border-color: transparent transparent black transparent;
}
.timestampEventTooltipText {
	font-size: 0.8em;
}
.timestampEventTooltipRight {
	left: -290px;
}
.timestampEventTooltipRight .timestampEventTooltipTriangle {
	left: 288px;
}
</style>