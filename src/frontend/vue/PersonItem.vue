<template>
    <router-link class="item" target="_blank" :to="personLink">
        <img :alt="person.name" :title="person.Name" :src="personImage">
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