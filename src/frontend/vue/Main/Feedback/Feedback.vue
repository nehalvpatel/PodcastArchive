<template>
    <div>
        <div :class="$style.pageTitle">
            <h2>Feedback</h2>
            <p :class="$style.pageDescription">Thank you for helping us improve our website. We apologise for any way our website may have inconvenienced you.</p>
        </div>
        <form @submit.prevent="submitFeedback">
            <div :class="$style.section">
                <h3 :class="$style.sectionTitle">Issue</h3>
                <div :class="$style.feedbackFields">
                    <input type="radio" v-model="issue" value="timeline_typo" id="timeline_typo">
                    <label for="timeline_typo" :class="$style.feedbackLabel">A spelling/grammar/punctuation/timing mistake in the website's timelines.</label>
                    <br>
                    <input type="radio" v-model="issue" value="browser_rendering" id="browser_rendering">
                    <label for="browser_rendering" :class="$style.feedbackLabel">A problem with browser rendering (the website doesn't look right).</label>
                    <br>
                    <input type="radio" v-model="issue" value="website_content" id="website_content">
                    <label for="website_content" :class="$style.feedbackLabel">A problem with the content on our website.</label>
                    <br>
                    <input type="radio" v-model="issue" value="other" id="otherwise">
                    <label for="otherwise" :class="$style.feedbackLabel">Other</label>
                </div>
            </div>
            <div :class="$style.section">
                <h3 :class="$style.sectionTitle">Explain</h3>
                <div :class="$style.feedbackFields">
                    <textarea v-model="explanation" :class="$style.feedbackTextarea"></textarea>
                </div>
            </div>
            <button type="submit" :class="$style.feedbackSubmitButton">Submit Feedback</button<
        </form>
    </div>
</template>

<script>
module.exports = {
    created: function() {
        this.$store.dispatch("markLaunched");
    },
    mounted: function() {
        document.title = "Feedback \u00B7 Painkiller Already";
    },
    data: function() {
        return {
            issue: "other",
            explanation: ""
        }
    },
    methods: {
        submitFeedback: function() {
            if (this.issue && this.explanation) {
                this.$store.dispatch("submitFeedback", {
                    feedbackIssue: this.issue,
                    feedbackExplanation: this.explanation
                }).then((messages) => {
                    this.$store.dispatch("displaySuccesses", messages);

                    this.issue = "";
                    this.explanation = "";
                }).catch((messages) => {
                    this.$store.dispatch("displayErrors", messages);
                });
            } else {
                this.$store.dispatch("displayErrors", ["Please make sure you selected an issue and filled out the explanation."]);
            }
        }
    }
}
</script>

<style module>
    .pageTitle {
        composes: pageTitle from "./../../Global.css"
    }

    .pageDescription {
        composes: pageDescription from "./../../Global.css"
    }

    .section {
        composes: section from "./../../Global.css"
    }

    .sectionTitle {
        composes: sectionHead3 from "./../../Global.css", sectionTitle from "./../../Global.css"
    }

    .feedbackFields {
        margin: 10px 0 0;
    }

    .feedbackLabel {
        -ms-word-break: break-all;
	    word-break: break-all;
    }

    .feedbackTextarea {
        composes: textarea from "./../../Global.css"
    }

    .feedbackSubmitButton {
        composes: textareaSubmitButton from "./../../Global.css"
    }
</style>