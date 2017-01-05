<template>
    <div>
        <div :class="$style.personImageContainer">
            <img :class="$style.personImage" :alt="person.Name" :title="person.Name" :src="image" />
        </div>
        <div :class="$style.personDetails">
            <div :class="$style.personOverview">
                <h2 :class="$style.sectionHeader" v-text="person.Name"></h2>
                <p v-text="person.Overview"></p>
            </div>
            <div v-if="person.SocialLinks.length > 0" :class="$style.socialIcons">
                <h2 :class="$style.sectionHeader">Social</h2>
                <a v-for="socialLink in person.SocialLinks" :class="$style.item" :href="socialLink.Link"><img :class="$style.itemImage" :alt="socialLink.Name" :title="socialLink.Name" :src="'/img/' + socialLink.Image"></a>
            </div>
            <div :class="$style.section">
                <h2 :class="$style.sectionHeader">Stats</h2>
                <p><span v-text="gender"></span> has hosted <strong v-text="person.HostCount"></strong> episode<span v-if="person.HostCount != 1">s</span>, been a guest on <strong v-text="person.GuestCount"></strong> episode<span v-if="person.GuestCount != 1">s</span>, and sponsored <strong v-text="person.SponsorCount"></strong> episode<span v-if="person.SponsorCount != 1">s</span>.</p>
            </div>
        </div>
        <div :class="$style.clear"></div>
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

<style module>
    .section {
        composes: section from "./../../Global.css"
    }
    .sectionHeader {
        composes: sectionHeader from "./../../Global.css"
    }
    .items {
        composes: items from "./../../Global.css"
    }
    .item {
        composes: item from "./../../Global.css"
    }
    .itemImage {
        composes: itemImage from "./../../Global.css"
    }
    .clear {
        composes: clear from "./../../Global.css"
    }
    .personImage {
        max-width: 100%;
        vertical-align: top;
    }



    .personImageContainer {
        max-height: 100%;
        padding: 10px;
        background: #333;
        float: left;
        text-align: center;
    }

    .personDetails {
        overflow: auto;
        padding-left: 20px;
    }

    .personOverview {
        composes: section from "./../../Global.css";
        margin-top: 0px;
    }

    .socialIcons {
        composes: items from "./../../Global.css", section from "./../../Global.css";
        float: none;
        margin-right: 0px;
    }

    @media (max-width: 1024px) {
        .personImageContainer, .personDetails {
            width: 100%;
        }
        .personImageContainer {
            text-align: center;
        }
        .personDetails {
            padding: 20px 0px 0px 0px;
        }
    }
</style>