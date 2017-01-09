import Vue from "vue";

import VueRouter from "vue-router";
Vue.use(VueRouter);

import Vuex from "vuex";
Vue.use(Vuex);

import VueYouTubeEmbed from "vue-youtube-embed";
Vue.use(VueYouTubeEmbed);

import VueMeta from "vue-meta";
Vue.use(VueMeta);

if (document.readyState === "complete") {
    initScript();
} else {
    document.addEventListener("DOMContentLoaded", function() {
        initScript();
    });
}

function fetchEpisodes(json) {
        var episodesJson = json;
        episodesJson["map"] = {};
        episodesJson["credits"] = {
            Loaded: false,
            developers: [],
            contributors: []
        };

        for (var episodeIdentifier in episodesJson["episodes"]) {
            if (!episodesJson["episodes"].hasOwnProperty(episodeIdentifier)) continue;

            episodesJson["episodes"][episodeIdentifier]["Loaded"] = false;
            episodesJson["episodes"][episodeIdentifier]["Description"] = "";
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

        return episodesJson;
};

function initScript() {
    var router = new VueRouter({
        base: "/",
        mode: "history",
        root: "/",
        routes: [
            {
                path: "/credits",
                component: require("./vue/Main/Credits/Credits.vue"),
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
                component: require("./vue/Main/Episode/Episode.vue"),
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
                component: require("./vue/Main/Episode/Episode.vue"),
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
                component: require("./vue/Main/Episode/Episode.vue"),
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
                component: require("./vue/Main/Feedback/Feedback.vue"),
                name: "feedback"
            },
            {
                path: "/login",
                component: require("./vue/Main/Login/Login.vue"),
                name: "login"
            },
            {
                path: "/person/:number",
                component: require("./vue/Main/Person/Person.vue"),
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
                component: require("./vue/Main/Error/Error.vue"),
                name: "error"
            }
        ]
    });

    router.afterEach((to) => {
        if (window.ga) {
            window.ga("set", "page", to.fullPath);
            window.ga("send", "pageview");
        }
    });

    var episodesJson = fetchEpisodes(JSON.parse(document.getElementById("data").textContent));

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
            episodeIdentifier: "",
            episodePlayer: null,
            highlightedTimestamp: null,
            loggedIn: episodesJson["loggedIn"],
            globalSuccesses: [],
            globalErrors: []
        },
        mutations: {
            markLaunched(state) {
                Vue.set(state, "firstLaunch", false);
            },
            showSuccesses(state, successes) {
                Vue.set(state, "globalSuccesses", successes);
            },
            clearSuccesses(state) {
                Vue.set(state, "globalSuccesses", []);
            },
            showErrors(state, errors) {
                Vue.set(state, "globalErrors", errors);
            },
            clearErrors(state) {
                Vue.set(state, "globalErrors", []);
            },
            login(state) {
                Vue.set(state, "loggedIn", true);
            },
            logout(state) {
                Vue.set(state, "loggedIn", false);
            },
            setEpisodeIdentifier(state, identifier) {
                Vue.set(state, "episodeIdentifier", identifier);
            },
            mountPlayer(state, player) {
                Vue.set(state, "episodePlayer", player);
            },
            cacheEpisode(state, data) {
                Vue.set(state.episodes, data.Identifier, Object.assign(state.episodes[data.Identifier], data));
            },
            cacheReddit(state, data) {
                Vue.set(state.episodes[data.Identifier], "RedditCount", data.RedditCount);
                Vue.set(state.episodes[data.Identifier], "RedditLink", data.RedditLink);
            },
            updateTimestamps(state, data) {
                Vue.set(state.episodes[data.Identifier], "Timeline", data.Payload.Timeline);
                Vue.set(state.episodes[data.Identifier], "Timelined", data.Payload.Timelined);
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
                Vue.set(state, "highlightedTimestamp", data.TimestampIndex);
            },
            clearHighlightedTimestamp(state) {
                Vue.set(state, "highlightedTimestamp", null);
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
            markLaunched(context, page) {
                if (context.state.firstLaunch) {
                    // disabled for now
                    /*if (document.querySelector(".router-link-active")) {
                        document.querySelector(".router-link-active").scrollIntoView();
                    }*/
                    context.commit("markLaunched");
                }

                context.dispatch("clearSuccesses");
                context.dispatch("clearErrors");

                context.dispatch("closeSidebar");
            },
            displaySuccesses(context, successes) {
                context.dispatch("clearErrors");

                if (successes.length > 0) {
                    context.commit("showSuccesses", successes);
                }
            },
            clearSuccesses(context) {
                if (context.state.globalSuccesses.length > 0) {
                    context.commit("clearSuccesses");
                }
            },
            displayErrors(context, errors) {
                context.dispatch("clearSuccesses");

                if (errors.length > 0) {
                    context.commit("showErrors", errors);
                }
            },
            clearErrors(context) {
                if (context.state.globalErrors.length > 0) {
                    context.commit("clearErrors");
                }
            },
            displayMessages(context, data) {
                if (data.type === "success") {
                    context.dispatch("displaySuccesses", data.data);
                } else {
                    context.dispatch("displayErrors", data.data);
                }
            },
            login(context, data) {
                return new Promise((resolve, reject) => {
                    var formData = new FormData();
                    formData.append("username", data.Username);
                    formData.append("password", data.Password);

                    fetch("/api/login.php", {
                        method: "POST",
                        body: formData,
                        credentials: "same-origin"
                    }).then((response) => {
                        var contentType = response.headers.get("content-type");
                        if (contentType && contentType.indexOf("application/json") !== -1) {
                            return response.json();
                        } else {
                            reject(["An error occured with the login request."]);
                        }
                    }).then((json) => {
                        if (json.type === "success") {
                            context.commit("login");

                            resolve(json.data);
                        } else {
                            reject(json.data);
                        }
                    });
                });
            },
            logout(context) {
                return new Promise((resolve, reject) => {
                    fetch("/api/logout.php", {
                        credentials: "same-origin"
                    }).then((response) => {
                        context.commit("logout");
                        resolve();
                    }).catch((error) => {
                        reject();
                    })
                });
            },
            setEpisodeIdentifier(context, identifier) {
                if (context.state.episodeIdentifier !== identifier) {
                    context.dispatch("clearHighlightedTimestamp");
                    context.commit("setEpisodeIdentifier", identifier);
                }
            },
            setEpisodeProgress(context, data) {
                localStorage.setItem(data.Identifier, data.Timestamp);
            },
            handleEpisodeNavigation(context, routeData) {
                return new Promise((resolve, reject) => {
                    if (routeData.name === "latest-episode") {
                        context.dispatch("fetchEpisode", context.state.latest.Identifier)
                            .then((data) => {
                                context.dispatch("markLaunched");
                                context.dispatch("setEpisodeIdentifier", context.state.latest.Identifier);
                            });

                        resolve();
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
                                });

                            resolve();
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
                        fetch("/api/episode.php?episode=" + context.state.episodes[episodeToFetch].Number)
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

                            context.dispatch("fetchPerson", personID);
                            
                            resolve();
                        } else {
                            reject("404");
                        }
                    }
                });
            },
            addTimeline(context, data) {
                return new Promise((resolve, reject) => {
                    var formData = new FormData();
                    formData.append("identifier", data.Identifier);
                    formData.append("timeline", data.formAddTimeline);

                    fetch("/api/addTimeline.php", {
                        method: "POST",
                        body: formData,
                        credentials: "same-origin"
                    }).then((response) => {
                        var contentType = response.headers.get("content-type");
                        if (contentType && contentType.indexOf("application/json") !== -1) {
                            return response.json();
                        } else {
                            reject(["An error occured with the timeline submit request."]);
                        }
                    }).then((json) => {
                        if (json.type === "success") {
                            context.commit("updateTimestamps", {
                                Identifier: data.Identifier,
                                Payload: json.payload
                            });

                            resolve(json.data);
                        } else {
                            reject(json.data);
                        }
                    });
                });
            },
            addTimestamp(context, data) {
                return new Promise((resolve, reject) => {
                    var formData = new FormData();
                    formData.append("identifier", data.Identifier);
                    formData.append("time", data.formAddTimestamp);
                    formData.append("event", data.formAddEvent);
                    formData.append("url", data.formAddURL);

                    fetch("/api/addTimestamp.php", {
                        method: "POST",
                        body: formData,
                        credentials: "same-origin"
                    }).then((response) => {
                        var contentType = response.headers.get("content-type");
                        if (contentType && contentType.indexOf("application/json") !== -1) {
                            return response.json();
                        } else {
                            reject(["An error occured with the timestamp submit request."]);
                        }
                    }).then((json) => {
                        if (json.type === "success") {
                            context.commit("updateTimestamps", {
                                Identifier: data.Identifier,
                                Payload: json.payload
                            });

                            resolve(json.data);
                        } else {
                            reject(json.data);
                        }
                    });
                });
            },
            updateTimestamp(context, data) {
                return new Promise((resolve, reject) => {
                    var formData = new FormData();
                    formData.append("id", data.timestampID);
                    formData.append("value", data.timestampEvent);
                    formData.append("url", data.timestampURL);

                    fetch("/api/updateTimestamp.php", {
                        method: "POST",
                        body: formData,
                        credentials: "same-origin"
                    }).then((response) => {
                        var contentType = response.headers.get("content-type");
                        if (contentType && contentType.indexOf("application/json") !== -1) {
                            return response.json();
                        } else {
                            reject(["An error occured with the timestamp update request."]);
                        }
                    }).then((json) => {
                        if (json.type === "success") {
                            context.commit("updateTimestamps", {
                                Identifier: data.Identifier,
                                Payload: json.payload
                            });

                            resolve(json.data);
                        } else {
                            reject(json.data);
                        }
                    });
                });
            },
            deleteTimestamp(context, data) {
                return new Promise((resolve, reject) => {
                    var formData = new FormData();
                    formData.append("id", data.timestampID);

                    fetch("/api/deleteTimestamp.php", {
                        method: "POST",
                        body: formData,
                        credentials: "same-origin"
                    }).then((response) => {
                        var contentType = response.headers.get("content-type");
                        if (contentType && contentType.indexOf("application/json") !== -1) {
                            return response.json();
                        } else {
                            reject(["An error occured with the timestamp delete request."]);
                        }
                    }).then((json) => {
                        if (json.type === "success") {
                            context.commit("updateTimestamps", {
                                Identifier: data.Identifier,
                                Payload: json.payload
                            });

                            resolve(json.data);
                        } else {
                            reject(json.data);
                        }
                    });
                });
            },
            fetchPerson(context, personToFetch) {
                return new Promise((resolve, reject) => {
                    if (context.state.people[personToFetch].Loaded) {
                        resolve();
                    } else {
                        fetch("/api/person.php?person=" + personToFetch)
                            .then((response) => {
                                return response.json();
                            }).then((json) => {
                                context.commit("cachePerson", json);
                            }).then(resolve());
                    }
                });
            },
            submitFeedback(context, data) {
                return new Promise((resolve, reject) => {
                    var formData = new FormData();
                    formData.append("issue", data.feedbackIssue);
                    formData.append("explanation", data.feedbackExplanation);

                    fetch("/api/feedback.php", {
                        method: "POST",
                        body: formData
                    }).then((response) => {
                        var contentType = response.headers.get("content-type");
                        if (contentType && contentType.indexOf("application/json") !== -1) {
                            return response.json();
                        } else {
                            reject(["An error occured with the feedback submit request."]);
                        }
                    }).then((json) => {
                        if (json.type === "success") {
                            resolve(json.data);
                        } else {
                            reject(json.data);
                        }
                    });
                });
            },
            fetchCredits(context) {
                return new Promise((resolve, reject) => {
                    if (context.state.Loaded) {
                        resolve();
                    } else {
                        fetch("/api/credits.php")
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
                if (context.state.highlightedTimestamp !== data.TimestampIndex) {
                    context.commit("highlightTimestamp", data);
                }
            },
            clearHighlightedTimestamp(context) {
                if (context.state.highlightedTimestamp !== null) {
                    context.commit("clearHighlightedTimestamp");
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
        el: "#app",
        router,
        store,
        render: createElement => createElement(require("./vue/App.vue"))
    });
}