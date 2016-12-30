<template>
    <tr :class="timestampClass">
        <td class="timestamp"><a class="timelink" :href="'/episode/' + number + '?timestamp=' + timestamp.Begin" v-on:click.prevent="seek" :data-begin="timestamp.Begin" :data-end="timestamp.End" v-text="timestamp.HMS"></a></td>
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
                "active-timestamp-vertical": this.timestamp.Highlighted
            };
        }
    }
};
</script>