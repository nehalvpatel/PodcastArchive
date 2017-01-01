<template>
    <div class="credits">
        <div id="page-title">
            <h2>Developers and Contributors</h2>
            <p>Thank you to everyone for making this website such a huge success. This website would not have become what it is without the immense effort our developers and contributors donate.</p>
        </div>
        <div class="section">
            <h3>Developers</h3>
            <ul>
                <li v-for="developer in developers">
                    <a :href="developer.DisplayLink" v-text="developer.DisplayName"></a>, <span v-text="developer.Praise"></span>
                </li>
            </ul>
        </div>
        <div class="section">
            <h3>Contributors</h3>
            <ul>
                <li v-for="contributor in contributors">
                    <a :href="contributor.DisplayLink" v-text="contributor.DisplayName"></a>, <span v-text="contributor.Praise"></span>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
module.exports = {
    created: function() {
        this.$store.dispatch("markLaunched");
    },
    mounted: function() {
        document.title = "Developers and Contributors \u00B7 Painkiller Already";
    },
    computed: {
        developers: function() {
            return this.$store.state.credits.developers;
        },
        contributors: function() {
            return this.$store.state.credits.contributors;
        }
    }
};
</script>