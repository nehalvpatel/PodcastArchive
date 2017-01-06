<template>
    <tr :class="timestampClass">
        <td v-if="this.$store.state.loggedIn" @click="deleteTimestamp" :class="$style.deleteTimestampButton">☓</td>
        <td :class="$style.timestampSeekCell"><a :class="$style.timestampSeekLink" :href="timestampLink" @click.prevent="seek" v-text="timestamp.HMS"></a></td>
        <td :class="$style.timestampEventCell">
            <form v-if="this.$store.state.loggedIn && showEditForm" @submit.prevent="updateTimestamp">
                <input :class="$style.updateTimestampText" type="text" v-model="formEditEvent" placeholder="The hosts talk about a topic" />
                <input :class="$style.updateTimestampText" type="text" v-model="formEditURL" placeholder="http://www.relevanturl.com (optional)" />
                <button :class="$style.updateFormSubmitButton" type="submit">Update Timestamp</button>
            </form>
            <span v-if="showEditForm === false">
                <a v-if="timestamp.URL" :class="$style.timestampURLLink" target="_blank" :href="timestamp.URL" v-text="timestamp.Value"></a>
                <span v-else v-text="timestamp.Value"></span>
            </span>
            <button v-if="this.$store.state.loggedIn" @click.prevent="toggleEditForm" :class="$style.editTimestampButton">Edit Timestamp</button>
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
            this.$store.state.episodePlayer.seekTo(this.timestamp.Begin);

            this.$router.replace({
                name: "specific-episode",
                params: {
                    number: this.episodeNumber
                },
                query: {
                    timestamp: this.timestamp.Begin
                }
            });
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
                [this.$style.timestampActive]: (this.$store.state.highlightedTimestamp === this.timestamp.ID)
            };
        },
        timestampLink: function() {
            return "/episode/" + this.episodeNumber + "?timestamp=" + this.timestamp.Begin;
        }
    }
};
</script>

<style module>
.timestampActive {
    font-weight: bold;
	background: #333;
}
.timestampSeekCell {
	width: 35px;
}
.timestampSeekLink {
	color: inherit;
}
.timestampEventCell {
	-ms-word-break: break-word;
	word-break: break-word;
}
.timestampURLLink {
	color: inherit;
}

.deleteTimestampButton {
	width: 20px;
    cursor: pointer;
    text-align: center;
}
.updateTimestampText {
	background: transparent;
	font-size: 10.5pt;
	color: white;
	font-family: 'Open Sans', sans-serif;
	border: 1px solid #999;
	width: 40%;
}
.updateFormSubmitButton, .editTimestampButton {
	float: right;
	padding: 3px;
	color: white;
	background: transparent;
	cursor: pointer;
    border: none;
    outline: none;
}
.updateFormSubmitButton:hover, .editTimestampButton:hover {
	text-decoration: underline;
}
</style>