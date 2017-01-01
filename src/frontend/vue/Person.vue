<template>
    <div class="person">
        <div id="image">
            <img id="person-image" class="person-image" :alt="person.Name" :title="person.Name" :src="image" />
        </div>
        <div id="details">
            <div id="overview" class="section">
                <h2 class="section-header" v-text="person.Name"></h2>
                <p v-text="person.Overview"></p>
            </div>
            <div v-if="person.SocialLinks.length > 0" id="social-icons" class="section items">
                <h2 class="section-header">Social</h2>
                <a v-for="socialLink in person.SocialLinks" class="item" :href="socialLink.Link"><img :alt="socialLink.Name" :title="socialLink.Name" :src="'/img/' + socialLink.Image"></a>
            </div>
            <div id="stats" class="section">
                <h2 class="section-header">Stats</h2>
                <p><span v-text="gender"></span> has hosted <strong v-text="person.HostCount"></strong> episode<span v-if="person.HostCount != 1">s</span>, been a guest on <strong v-text="person.GuestCount"></strong> episode<span v-if="person.GuestCount != 1">s</span>, and sponsored <strong v-text="person.SponsorCount"></strong> episode<span v-if="person.SponsorCount != 1">s</span>.</p>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</template>

<script>
module.exports = {
    created: function() {
        this.$store.dispatch("markLaunched");
    },
    computed: {
        person: function() {
            return this.$store.state.people[this.$route.params.number];
        },
        image: function() {
            return "/img/people/" + this.person.ID + "a.png";
        },
        gender: function() {
            if (this.person.Gender == "1") {
                return "He";
            } else {
                return "She";
            }
        }
    },
    watch: {
        person: function() {
            document.title = this.person.Name + " \u00B7 Painkiller Already";
        }
    }
}
</script>