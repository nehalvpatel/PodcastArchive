<template>
    <a class="timelink" :href="'/episode/' + number + '?timestamp=' + timestamp.Begin" v-on:click.prevent="seek" :data-begin="timestamp.Begin" :data-end="timestamp.End">
        <div class="topic" :class="timestampClass" :style="'width: ' + timestamp.Width + '%'">
            <div class="tooltip" :class="{ right: timestamp.Right }" :id="timestamp.ID">
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
        number: {
            type: String,
            required: true
        }
    },
    methods: {
        seek: function() {
            this.$emit("seek", this.timestamp.Begin);
        }
    },
    computed: {
        timestampClass: function() {
            return {
                "active-timestamp-horizontal": this.timestamp.Highlighted
            };
        }
    }
};
</script>