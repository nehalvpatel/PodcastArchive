<template>
    <tr :class="timestampClass">
        <td v-if="this.$store.state.loggedIn" class="delete">
            <form @submit.prevent="deleteTimestamp" method="POST">
                <input type="submit" value="☓" />
            </form>
        </td>
        <td class="timestamp"><a class="timelink" :href="timestampLink" @click.prevent="seek" v-text="timestamp.HMS"></a></td>
        <td class="event">
            <form v-if="this.$store.state.loggedIn && showEditForm" @submit.prevent="updateTimestamp" class="updateTimestampForm">
                <input type="text" v-model="formEditEvent" />
                <input type="text" v-model="formEditURL" />
                <input type="submit" value="Update Timestamp" />
            </form>
            <span v-if="showEditForm === false">
                <a v-if="timestamp.URL" target="_blank" :href="timestamp.URL" v-text="timestamp.Value"></a>
                <span v-else v-text="timestamp.Value"></span>
            </span>
            <button v-if="this.$store.state.loggedIn" @click.prevent="toggleEditForm" class="editTimestamp">Edit Timestamp</button>
        </td>
    </tr>
</template>

<script>
module.exports = {
    data: function() {
        return {
            formEditEvent: this.timestamp.Value,
            formEditURL: this.timestamp.URL,
            showEditForm: false
        };
    },
    props: {
        timestamp: {
            type: Object,
            required: true
        },
        episodeNumber: {
            type: Number,
            required: true
        }
    },
    methods: {
        seek: function() {
            this.$emit("seek", this.episodeNumber, this.timestamp);
        },
        toggleEditForm: function() {
            this.showEditForm = !this.showEditForm;
        },
        updateTimestamp: function() {
            if (this.formEditEvent) {
                this.$store.dispatch("updateTimestamp", {
                    Identifier: this.$store.state.map[this.episodeNumber],
                    timestampID: this.timestamp.ID,
                    timestampEvent: this.formEditEvent,
                    timestampURL: this.formEditURL
                }).then((messages) => {
                    this.$store.dispatch("displaySuccesses", messages);

                    this.showEditForm = false;
                }).catch((messages) => {
                    this.$store.dispatch("displayErrors", messages);
                });
            } else {
                this.$store.dispatch("displayErrors", ["Please make sure you filled in the timestamp event."]);
            }
        },
        deleteTimestamp: function() {
            this.$store.dispatch("deleteTimestamp", {
                Identifier: this.$store.state.map[this.episodeNumber],
                timestampID: this.timestamp.ID
            }).then((messages) => {
                this.$store.dispatch("displaySuccesses", messages);
            }).catch((messages) => {
                this.$store.dispatch("displayErrors", messages);
            });
        }
    },
    computed: {
        timestampClass: function() {
            return {
                "active-timestamp-vertical": this.timestamp.Highlighted
            };
        },
        timestampLink: function() {
            return "/episode/" + this.episodeNumber + "?timestamp=" + this.timestamp.Begin;
        }
    }
};
</script>