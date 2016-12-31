<template>
    <aside class="sidebar" :class="sidebarClass">
        <nav id="sidebar">
            <div class="search-form"><input class="search-field" type="search" id="search-field" name="search" placeholder="Search"></div>
            <h3><span>Episodes</span><router-link title="Random Episode" to="/episode/random" class="random-button timelined"><i class="icon-random"></i></router-link></h3>
            <div id="search-error" class="error">
                <p></p>
            </div>
            <ul>
                <sidebar-item v-for="episode in episodes" :key="episode.Identifier" :number="episode.Number" :highlighted="episode.Highlighted" :timelined="episode.Timelined"></sidebar-item>
            </ul>
        </nav>
    </aside>-
</template>

<script>
module.exports = {
    props: ["episodes"],
    computed: {
        sidebarClass: function() {
            return {
                toggled: this.$store.state.sidebarOpen
            }
        }
    }
}
</script>