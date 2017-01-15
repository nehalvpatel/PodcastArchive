<template>
    <div :class="$style.searchForm">
        <input v-model.trim="searchQuery" :class="$style.searchBar" type="search" name="search" placeholder="Search">
    </div>
</template>

<script>
module.exports = {
    created: function() {
        this.searchQuery = this.$route.query.query;
    },
    data: function() {
        return {
            searchTimer: null,
            searchQuery: "",
            previousQuery: ""
        }
    },
    watch: {
        searchQuery: function(newVal, oldVal) {
            this.handleSearch(newVal);
        },
        "$route.query.query": function(newVal) {
            this.searchQuery = newVal;
        }
    },
    methods: {
        handleSearch: function(newVal) {
            clearTimeout(this.searchTimer);
            
            this.searchTimer = setTimeout(() => {
                if (newVal) {
                    if (newVal !== this.previousQuery) {
                        if (window.ga) {
                            window.ga("send", {
                                hitType: "event",
                                eventCategory: "Search",
                                eventAction: "Search",
                                eventLabel: newVal
                            })
                        }

                        fetch("/api/search.php?query=" + encodeURIComponent(newVal))
                            .then((response) => {
                                return response.json();
                            }).then((json) => {
                                this.previousQuery = newVal;

                                this.$store.dispatch("hideSearchError");
                                this.$store.dispatch("clearSearchResults");
                                this.$store.dispatch("enableSearchMode");
                                
                                if (json.count > 0) {
                                    for (var identifier in json.results) {
                                        if (!json.results.hasOwnProperty(identifier)) continue;

                                        var episodeResults = {
                                            Identifier: identifier,
                                            SearchResults: json.results[identifier]
                                        };

                                        this.$store.commit("addSearchResults", episodeResults);
                                    }
                                }
                            }).catch((error) => {
                                this.previousQuery = newVal;

                                this.$store.dispatch("showSearchError");
                                this.$store.dispatch("clearSearchResults");
                                this.$store.dispatch("enableSearchMode");
                            });
                    }
                } else {
                    this.previousQuery = "";

                    this.$store.dispatch("hideSearchError");
                    this.$store.dispatch("clearSearchResults");
                    this.$store.dispatch("disableSearchMode");
                }
            }, 1000);
        }
    }
}
</script>

<style module>
.searchForm {
	padding: 7px;
}
.searchBar {
	width: 100%;
	border-style: none;
	padding: 5px 10px 5px 10px;
	background: #555;
    -moz-appearance: textfield;
	-webkit-appearance: textfield;
    outline: none;
    border: none;
}
.searchBar:focus {
	background: #777;
}
</style>