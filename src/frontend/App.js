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
        episodesJson["map"] = {};
        episodesJson["credits"] = {
            Loaded: false,
            developers: [],
            contributors: []
        };

        for (var episodeIdentifier in episodesJson["episodes"]) {
            if (!episodesJson["episodes"].hasOwnProperty(episodeIdentifier)) continue;

            episodesJson["episodes"][episodeIdentifier]["Loaded"] = false;
            episodesJson["episodes"][episodeIdentifier]["SearchResults"] = [];
            episodesJson["episodes"][episodeIdentifier]["Timeline"] = {
                Timestamps: []
            }

            episodesJson["map"][episodesJson["episodes"][episodeIdentifier]["Number"]] = episodeIdentifier;
        }

        for (var personID in episodesJson["people"]) {
            if (!episodesJson["people"].hasOwnProperty(personID)) continue;

            episodesJson["people"][personID] = {
                ID: personID,
                Name: "",
                Overview: "",
                SocialLinks: [],
                HostCount: 0,
                GuestCount: 0,
                SponsorCount: 0,
                Gender: 1
            };
        }

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
                name: "credits",
                beforeEnter: function(to, from, next) {
                    store.dispatch("fetchCredits", to)
                        .then((data) => {
                            next();
                        });
                }
            },
            {
                path: "/",
                component: Episode,
                name: "latest-episode",
                beforeEnter: function(to, from, next) {
                    store.dispatch("handleEpisodeNavigation", to)
                        .then((data) => {
                            next();
                        });
                }
            },
            {
                path: "/episode/random",
                component: Episode,
                name: "random-episode",
                beforeEnter: function(to, from, next) {
                    store.dispatch("handleEpisodeNavigation", to)
                        .then((data) => {
                            next({
                                name: "specific-episode",
                                params: {
                                    number: data
                                }
                            });
                        });
                }
            },
            {
                path: "/episode/:number",
                component: Episode,
                name: "specific-episode",
                beforeEnter: function(to, from, next) {
                    store.dispatch("handleEpisodeNavigation", to)
                        .then((data) => {
                            next();
                        }).catch((error) => {
                            next({
                                path: "/404"
                            });
                        });
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
                    store.dispatch("handlePersonNavigation", to)
                        .then((data) => {
                            next();
                        }).catch((error) => {
                            next({
                                path: "/404"
                            });
                        });
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
            searchMode: false,
            episodeIdentifier: ""
        },
        mutations: {
            markLaunched(state) {
                Vue.set(state, "firstLaunch", false);
            },
            setEpisodeIdentifier(state, identifier) {
                Vue.set(state, "episodeIdentifier", identifier);
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
            cacheCredits(state, data) {
                Vue.set(state, "credits", data);
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
                    if (document.querySelector(".router-link-active")) {
                        document.querySelector(".router-link-active").scrollIntoView();
                    }
                    context.commit("markLaunched");
                }

                context.dispatch("closeSidebar");
            },
            setEpisodeIdentifier(context, identifier) {
                if (context.state.episodeIdentifier !== identifier) {
                    context.dispatch("clearAllHighlighted", identifier);
                    context.commit("setEpisodeIdentifier", identifier);
                }
            },
            handleEpisodeNavigation(context, routeData) {
                return new Promise((resolve, reject) => {
                    if (routeData.name === "latest-episode") {
                        context.dispatch("fetchEpisode", context.state.latest.Identifier)
                            .then((data) => {
                                context.dispatch("markLaunched");
                                context.dispatch("setEpisodeIdentifier", context.state.latest.Identifier);
                            }).then(resolve());
                    } else if (routeData.name === "random-episode") {
                        var episodeKeys = Object.keys(context.state.episodes);
                        var episodeIdentifier = episodeKeys[episodeKeys.length * Math.random() << 0];
                        context.dispatch("setEpisodeIdentifier", episodeIdentifier);

                        resolve(context.state.episodes[episodeIdentifier].Number);
                    } else if (routeData.name === "specific-episode") {
                        if (context.state.map.hasOwnProperty(routeData.params.number)) {
                            var episodeIdentifier = context.state.map[routeData.params.number]

                            context.dispatch("fetchEpisode", episodeIdentifier)
                                .then((data) => {
                                    context.dispatch("markLaunched");
                                    context.dispatch("setEpisodeIdentifier", episodeIdentifier);
                                }).then(resolve());
                        } else {
                            reject("404");
                        }
                    }
                });
            },
            fetchEpisode(context, episodeToFetch) {
                return new Promise((resolve, reject) => {
                    if (context.state.episodes[episodeToFetch].Loaded) {
                        resolve();
                    } else {
                        fetch("/api/episodes/" + context.state.episodes[episodeToFetch].Number + ".json")
                            .then((response) => {
                                return response.json();
                            }).then((json) => {
                                context.commit("cacheEpisode", json);

                                if (json.Reddit) {
                                    context.dispatch("fetchRedditCount", {
                                        identifierToFetch: json.Identifier,
                                        Reddit: json.Reddit
                                    });
                                }
                            }).then(resolve());
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
            handlePersonNavigation(context, routeData) {
                return new Promise((resolve, reject) => {
                    if (routeData.name === "specific-person") {
                        if (context.state.people.hasOwnProperty(routeData.params.number)) {
                            var personID = routeData.params.number;

                            context.dispatch("fetchPerson", personID).then(resolve());
                        } else {
                            reject("404");
                        }
                    }
                });
            },
            fetchPerson(context, personToFetch) {
                return new Promise((resolve, reject) => {
                    if (context.state.people[personToFetch].Loaded) {
                        resolve();
                    } else {
                        fetch("/api/people/" + personToFetch + ".json")
                            .then((response) => {
                                return response.json();
                            }).then((json) => {
                                context.commit("cachePerson", json);
                            }).then(resolve());
                    }
                });
            },
            fetchCredits(context) {
                return new Promise((resolve, reject) => {
                    if (context.state.Loaded) {
                        resolve();
                    } else {
                        fetch("/api/credits.json")
                            .then((response) => {
                                return response.json();
                            }).then((json) => {
                                context.commit("cacheCredits", json);
                            }).then(resolve());
                    }
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