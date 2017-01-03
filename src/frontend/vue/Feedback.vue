<template>
    <div class="feedback">
        <div id="page-title">
            <h2>Feedback</h2>
            <p>Thank you for helping us improve our website. We apologise for any way our website may have inconvenienced you.</p>
        </div>
        <form @submit.prevent="submitFeedback">
            <div class="section">
                <h3>Issue</h3>
                <div>
                    <input type="radio" v-model="issue" value="timeline_typo" id="timeline_typo">
                    <label for="timeline_typo">A spelling/grammar/punctuation/timing mistake in the website's timelines.</label>
                    <br>
                    <input type="radio" v-model="issue" value="browser_rendering" id="browser_rendering">
                    <label for="browser_rendering">A problem with browser rendering (the website doesn't look right).</label>
                    <br>
                    <input type="radio" v-model="issue" value="website_content" id="website_content">
                    <label for="website_content">A problem with the content on our website.</label>
                    <br>
                    <input type="radio" v-model="issue" value="other" id="otherwise">
                    <label for="otherwise">Other</label>
                </div>
            </div>
            <div class="section">
                <h3>Explain</h3>
                <div>
                    <textarea v-model="explanation" id="explanation" rows="5"></textarea>
                </div>
            </div>
            <input type="submit" value="Submit Feedback">
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