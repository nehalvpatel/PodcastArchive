// dependencies
var Vue = require("vue");
var VueRouter = require("vue-router");
var Vuex = require("vuex");
var VueYouTubeEmbed = require("vue-youtube-embed");

// components
var App = require("./vue/App.vue");
var Credits = require("./vue/Credits.vue");
var Episode = require("./vue/Episode.vue");
var Sidebar = require("./vue/Sidebar.vue");
var SidebarItem = require("./vue/SidebarItem.vue");
var SearchResult = require("./vue/SearchResult.vue");
var PersonItem = require("./vue/PersonItem.vue");
var HorizontalTimestamp = require("./vue/HorizontalTimestamp.vue");
var VerticalTimestamp = require("./vue/VerticalTimestamp.vue");
var Feedback = require("./vue/Feedback.vue");
var Person = require("./vue/Person.vue");
var Error = require("./vue/Error.vue");

Vue.use(VueRouter);
Vue.use(Vuex);
Vue.use(VueYouTubeEmbed);
Vue.component("sidebar", Sidebar);
Vue.component("sidebar-item", SidebarItem);
Vue.component("search-result", SearchResult);
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

var jsonLoaded = false;
var episodesJson = {};
fetch("/api/episodes/all.json")
    .then((response) => {
        return response.json();
    }).then((json) => {
        episodesJson = json;
        jsonLoaded = true;
        launch();
    });

function launch() {
    if (domLoaded && jsonLoaded) {
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
                path: "/credits",
                component: Credits,
                name: "credits"
            },
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
                name: "specific-episode",
                beforeEnter: function(to, from, next) {
                    if (store.state.map.hasOwnProperty(to.params.number)) {
                        next();
                    } else {
                        next({
                            path: "/404"
                        });
                    }
                },
            },
            {
                path: "/feedback",
                component: Feedback,
                name: "feedback"
            },
            {
                path: "/person/:number",
                component: Person,
                name: "specific-person",
                beforeEnter: function(to, from, next) {
                    if (store.state.people.hasOwnProperty(to.params.number)) {
                        next();
                    } else {
                        next({
                            path: "/404"
                        });
                    }
                }
            },
            {
                path: "*",
                component: Error,
                name: "error"
            }
        ]
    });

    const store = new Vuex.Store({
        state: {
            credits: episodesJson["credits"],
            episodes: episodesJson["episodes"],
            map: episodesJson["map"],
            latest: episodesJson["latest"],
            people: episodesJson["people"],
            firstLaunch: true,
            sidebarOpen: false,
            searchError: false,
            searchMode: false
        },
        mutations: {
            markLaunched(state) {
                Vue.set(state, "firstLaunch", false);
            },
            cacheEpisode(state, data) {
                Vue.set(state.episodes, data.Identifier, data);
            },
            cacheReddit(state, data) {
                Vue.set(state.episodes[data.Identifier], "RedditCount", data.RedditCount);
                Vue.set(state.episodes[data.Identifier], "RedditLink", data.RedditLink);
            },
            cachePerson(state, data) {
                Vue.set(state.people, data.ID, data);
            },
            openSidebar(state) {
                Vue.set(state, "sidebarOpen", true);
            },
            closeSidebar(state) {
                Vue.set(state, "sidebarOpen", false);
            },
            highlightTimestamp(state, data) {
                Vue.set(state.episodes[data.Identifier].Timeline.Timestamps[data.TimestampIndex], "Highlighted", true);
            },
            unhighlightTimestamp(state, data) {
                Vue.set(state.episodes[data.Identifier].Timeline.Timestamps[data.TimestampIndex], "Highlighted", false);
            },
            showSearchError(state) {
                Vue.set(state, "searchError", true);
            },
            hideSearchError(state) {
                Vue.set(state, "searchError", false);
            },
            enableSearchMode(state) {
                Vue.set(state, "searchMode", true);
            },
            disableSearchMode(state) {
                Vue.set(state, "searchMode", false);
            },
            addSearchResults(state, data) {
                Vue.set(state.episodes[data.Identifier], "SearchResults", data.SearchResults);
            },
            removeSearchResults(state, identifier) {
                Vue.set(state.episodes[identifier], "SearchResults", []);
            }
        },
        actions: {
            markLaunched(context) {
                if (context.state.firstLaunch) {
                    context.commit("markLaunched");
                }
            },
            fetchEpisode(context, episodeToFetch) {
                fetch("/api/episodes/" + episodeToFetch + ".json")
                    .then((response) => {
                        return response.json();
                    }).then((json) => {
                        context.commit("cacheEpisode", json);
                        context.dispatch("closeSidebar");

                        if (context.state.firstLaunch) {
                            document.querySelector(".router-link-active").scrollIntoView();
                            context.dispatch("markLaunched");
                        }

                        if (json.Reddit) {
                            context.dispatch("fetchRedditCount", {
                                identifierToFetch: json.Identifier,
                                Reddit: json.Reddit
                            });
                        }
                    });
            },
            fetchRedditCount(context, data) {
                fetch("https://www.reddit.com/comments/" + data.Reddit + ".json")
                    .then((response) => {
                        return response.json();
                    }).then((json) => {
                        var redditPayload = {
                            Identifier: data.identifierToFetch,
                            RedditCount: json[0].data.children[0].data.num_comments,
                            RedditLink: "https://www.reddit.com" + json[0].data.children[0].data.permalink
                        };

                        context.commit("cacheReddit", redditPayload);
                    });
            },
            fetchPerson(context, personToFetch) {
                fetch("/api/people/" + personToFetch + ".json")
                    .then((response) => {
                        return response.json();
                    }).then((json) => {
                        context.commit("cachePerson", json);
                    });
            },
            closeSidebar(context) {
                if (context.state.sidebarOpen) {
                    context.commit("closeSidebar");
                }
            },
            toggleSidebar(context) {
                if (context.state.sidebarOpen) {
                    context.commit("closeSidebar");
                } else {
                    context.commit("openSidebar");
                }
            },
            highlightTimestamp(context, data) {
                if (!data.Highlighted) {
                    context.commit("highlightTimestamp", data);
                }
            },
            unhighlightTimestamp(context, data) {
                if (data.Highlighted) {
                    context.commit("unhighlightTimestamp", data);
                }
            },
            clearAllHighlighted(context, identifier) {
                for (var i = 0, timestampCount = context.state.episodes[identifier].Timeline.Timestamps.length; i < timestampCount; i++) {
                    context.dispatch("unhighlightTimestamp", {
                        Identifier: identifier,
                        Highlighted: context.state.episodes[identifier].Timeline.Timestamps[i].Highlighted,
                        TimestampIndex: i
                    });
                }
            },
            showSearchError(context) {
                if (!context.state.searchError) {
                    context.commit("showSearchError");
                }
            },
            hideSearchError(context) {
                if (context.state.searchError) {
                    context.commit("hideSearchError");
                }
            },
            enableSearchMode(context) {
                if (!context.state.searchMode) {
                    context.commit("enableSearchMode");
                }
            },
            disableSearchMode(context) {
                if (context.state.searchMode) {
                    context.commit("disableSearchMode");
                }
            },
            clearSearchResults(context) {
                for (var identifier in context.state.episodes) {
                    if (!context.state.episodes.hasOwnProperty(identifier)) continue;

                    if (context.state.episodes[identifier].SearchResults.length > 0) {
                        context.commit("removeSearchResults", identifier);
                    }
                }
            },
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