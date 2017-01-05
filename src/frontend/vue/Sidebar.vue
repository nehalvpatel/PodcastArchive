<template>
    <aside :class="sidebarClass">
        <nav :class="$style.sidebar">
            <search-bar></search-bar>
            <h3 :class="$style.sidebarTitle" ><span>Episodes</span><router-link title="Random Episode" to="/episode/random" :class="$style.randomButton"><i :class="$style.randomButtonIcon" class="icon-random"></i></router-link></h3>
            <div v-if="this.$store.state.searchError" :class="$style.searchError">
                <p :class="$style.searchErrorText"></p>
            </div>
            <ul>
                <sidebar-item v-for="episode in episodes" :key="episode.Identifier" :number="episode.Number" :identifier="episode.Identifier" :searchResults="episode.SearchResults" :timelined="episode.Timelined"></sidebar-item>
            </ul>
        </nav>
    </aside>
</template>

<script>
module.exports = {
    props: {
        episodes: {
            type: Object,
            required: true
        }
    },
    computed: {
        sidebarClass: function() {
            return {
                [this.$style.sidebarPane]: true,
                [this.$style.sidebarPaneWhenOpen]: this.$store.state.sidebarOpen,
                [this.$style.sidebarPaneWhenClosed]: !this.$store.state.sidebarOpen
            }
        }
    }
}
</script>

<style module>
.sidebarPane {
	composes: pane from "./Global.css";
	width: 230px;
	background: #333;
	overflow: hidden;
}

.sidebarPaneWhenOpen {
    composes: paneWhenSidebarOpen from "./Global.css"
}

.sidebarPaneWhenClosed {
    composes: paneWhenSidebarClosed from "./Global.css"
}

.sidebar {
	height: 100%;
	overflow: auto;
}

.sidebarTitle {
    composes: sectionHead3 from "./Global.css";
}

.searchError {
    composes: error from "./Global.css";
}

.searchErrorText {
	padding: 20px;
}

.searchErrorText::before {
	content: 'There was an error with the search engine.';
	white-space: pre-wrap;
}

.randomButton {
    composes: label from "./Global.css";
    display: inline-block;
	margin: 4px -8px 0 0 !important;
	padding: 0 !important;
	text-decoration: none;
}

.randomButtonIcon {
    font-size: 12px;
	vertical-align: top;
	padding: 4px;
}
</style>