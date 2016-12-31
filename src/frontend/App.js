// dependencies
var Vue = require("vue");
var VueRouter = require("vue-router");
var Vuex = require("vuex");
var VueYouTubeEmbed = require("vue-youtube-embed");

// components
var App = require("./vue/App.vue");
var Episode = require("./vue/Episode.vue");
var Sidebar = require("./vue/Sidebar.vue");
var SidebarItem = require("./vue/SidebarItem.vue");
var PersonItem = require("./vue/PersonItem.vue");
var HorizontalTimestamp = require("./vue/HorizontalTimestamp.vue");
var VerticalTimestamp = require("./vue/VerticalTimestamp.vue");

Vue.use(VueRouter);
Vue.use(Vuex);
Vue.use(VueYouTubeEmbed);
Vue.component("sidebar", Sidebar);
Vue.component("sidebar-item", SidebarItem);
Vue.component("person-item", PersonItem);
Vue.component("horizontal-timestamp", HorizontalTimestamp);
Vue.component("vertical-timestamp", VerticalTimestamp);

var domLoaded = false;
if (document.readyState === "complete") {
    domLoaded = true;
    launch();
} else {
    document.addEventListener("DOMContentLoaded", function() {
        domLoaded = true;
        launch();
    });
}

var episodesLoaded = false;
var episodesJson = {};
fetch("/api/json/episodes.json")
    .then((response) => {
        return response.json();
    }).then((json) => {
        episodesJson = json;
        episodesLoaded = true;
        launch();
    });

function launch() {
    if (domLoaded && episodesLoaded) {
        initScript();
    }
}

function initScript() {
    var router = new VueRouter({
        base: "/",
        mode: "history",
        root: "/",
        routes: [
            {
                path: "/",
                component: Episode,
                name: "latest-episode"
            },
            {
                path: "/episode/random",
                component: Episode,
                name: "random-episode"
            },
            {
                path: "/episode/:number",
                component: Episode,
                name: "specific-episode"
            }
        ]
    });

    const store = new Vuex.Store({
        state: {
            episodes: episodesJson["episodes"],
            map: episodesJson["map"],
            latest: episodesJson["latest"],
            sidebarOpen: false
        },
        mutations: {
            cacheEpisode(state, data) {
                state.episodes[data.Identifier] = data;
            },
            cacheReddit(state, data) {
                Vue.set(state.episodes[data.Identifier], "RedditCount", data.RedditCount);
                Vue.set(state.episodes[data.Identifier], "RedditLink", data.RedditLink);
            },
            openSidebar(state) {
                state.sidebarOpen = true;
            },
            closeSidebar(state) {
                state.sidebarOpen = false;
            },
            highlightTimestamp(state, data) {
                Vue.set(state.episodes[data.Identifier].Timeline.Timestamps[data.TimestampIndex], "Highlighted", true);
            },
            unhighlightTimestamp(state, data) {
                Vue.set(state.episodes[data.Identifier].Timeline.Timestamps[data.TimestampIndex], "Highlighted", false);
            }
        },
        actions: {
            toggleSidebar(context, currentlyOpen) {
                if (currentlyOpen) {
                    context.commit("closeSidebar");
                } else {
                    context.commit("openSidebar");
                }
            },
            clearHighlighted(context, identifier) {
                for (var i = 0; i < context.state.episodes[identifier].Timeline.Timestamps.length; i++) {
                    if (context.state.episodes[identifier].Timeline.Timestamps[i].Highlighted) {
                        context.commit("unhighlightTimestamp", {
                            Identifier: identifier,
                            TimestampIndex: i
                        });
                    }
                }
            }
        }
    });

    new Vue({
        router,
        store,
        render(h) {
            return h(App);
        }
    }).$mount("#app");
}