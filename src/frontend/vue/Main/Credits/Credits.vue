<template>
    <div>
        <div :class="$style.pageTitle">
            <h2>Developers and Contributors</h2>
            <p :class="$style.pageDescription">Thank you to everyone for making this website such a huge success. This website would not have become what it is without the immense effort our developers and contributors donate.</p>
        </div>
        <div :class="$style.section">
            <h3 :class="$style.sectionTitle">Developers</h3>
            <ul :class="$style.sectionList">
                <li v-for="developer in developers">
                    <a :href="developer.DisplayLink" :class="$style.sectionItemLink" v-text="developer.DisplayName"></a>, <span v-text="developer.Praise"></span>
                </li>
            </ul>
        </div>
        <div :class="$style.section">
            <h3 :class="$style.sectionTitle">Contributors</h3>
            <ul :class="$style.sectionList">
                <li v-for="contributor in contributors">
                    <a :href="contributor.DisplayLink" :class="$style.sectionItemLink" v-text="contributor.DisplayName"></a>, <span v-text="contributor.Praise"></span>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
module.exports = {
    metaInfo: function() {
        return {
            title: "Developers and Contributors",
            titleTemplate: "%s · Painkiller Already",
        };
    },
    created: function() {
        this.$store.dispatch("markLaunched");
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

<style module>
    .pageTitle {
        composes: pageTitle from "./../../Global.css"
    }

    .pageDescription {
        composes: pageDescription from "./../../Global.css"
    }

    .section {
        composes: section from "./../../Global.css"
    }
    .sectionTitle {
        composes: sectionHead3 from "./../../Global.css", sectionTitle from "./../../Global.css"
    }
    .sectionList {
        margin: 10px 0 0;
        padding-left: 30px;
        list-style-type: square;
    }
    .sectionItemLink {
        color: white;
	    font-weight: bold;
    }
</style>