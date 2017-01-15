<template>
    <div :class="$style.error">
        <img :class="$style.errorImage" alt="Lefty" title="Lefty" src="/img/404.png">
        <h2 :class="$style.errorText"><span v-text="code"></span> <span v-text="status"></span></h2>
        <p>Blame the government!</p>
    </div>
</template>

<script>
module.exports = {
    data: function() {
        return {
            code: "404",
            status: "Not Found"
        }
    }
}
</script>

<style module>
.error {
    composes: error from "./../../Global.css"
}

.errorImage {
	margin-bottom: 50px;
	max-width: 747px;
	width: 100%;
}

.errorText {
	font-size: 3em;
}
</style>