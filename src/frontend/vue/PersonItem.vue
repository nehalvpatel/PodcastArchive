<template>
    <a class="item" target="_blank" :href="personLink">
        <img :alt="person.name" :title="person.Name" :src="personImage">
    </a>
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