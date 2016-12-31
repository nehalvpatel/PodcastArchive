<template>
    <aside class="sidebar" :class="sidebarClass">
        <nav id="sidebar">
            <div class="search-form"><input v-model="searchQuery" class="search-field" type="search" id="search-field" name="search" placeholder="Search"></div>
            <h3><span>Episodes</span><router-link title="Random Episode" to="/episode/random" class="random-button timelined"><i class="icon-random"></i></router-link></h3>
            <div v-if="this.$store.state.searchError" id="search-error" class="error">
                <p></p>
            </div>
            <ul>
                <sidebar-item v-for="episode in episodes" :key="episode.Identifier" :number="episode.Number" :highlighted="episode.Highlighted" :searchResults="episode.SearchResults" :timelined="episode.Timelined"></sidebar-item>
            </ul>
        </nav>
    </aside>-
</template>

<script>
module.exports = {
    props: ["episodes"],
    data: function() {
        return {
            searchTimer: null,
            searchQuery: this.$route.query.query,
            previousQuery: ""
        }
    },
    computed: {
        sidebarClass: function() {
            return {
                toggled: this.$store.state.sidebarOpen
            }
        }
    },
    watch: {
        searchQuery: function(newVal, oldVal) {
            clearTimeout(this.searchTimer);
            
            this.searchTimer = setTimeout(() => {
                if (newVal.trim()) {
                    fetch("/api/search.php?query=" + encodeURIComponent(newVal))
                        .then((response) => {
                            return response.json();
                        }).then((json) => {
                            this.previousQuery = newVal;

                            this.$store.commit("hideSearchError");
                            this.$store.dispatch("clearSearchResults");
                            this.$store.commit("enableSearchMode");
                            
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

                            this.$store.commit("showSearchError");
                            this.$store.dispatch("clearSearchResults");
                            this.$store.commit("enableSearchMode");
                        });
                } else {
                    this.previousQuery = "";

                    this.$store.commit("hideSearchError");
                    this.$store.dispatch("clearSearchResults");
                    this.$store.commit("disableSearchMode");
                }
            }, 1000);
        }
    }
}
</script>