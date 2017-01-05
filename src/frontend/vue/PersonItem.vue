<template>
    <router-link :class="$style.item" target="_blank" :to="personLink">
        <img :class="$style.itemImage" :alt="person.name" :title="person.Name" :src="personImage">
    </router-link>
</template>

<script>
module.exports = {
    computed: {
        personImage: function() {
            return "/img/people/" + this.person.ID + ".png";
        },
        personLink: function() {
            return "/person/" + this.person.ID;
        }
    },
    props: {
        person: {
            type: Object,
            required: true
        }
    }
}
</script>

<style module>
    .item {
        composes: item from "./Global.css"
    }
    .itemImage {
        composes: itemImage from "./Global.css"
    }
</style>