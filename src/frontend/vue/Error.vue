<template>
    <div class="error">
        <img alt="Lefty" title="Lefty" src="/img/404.png">
        <h2><span v-text="code"></span> <span v-text="status"></span></h2>
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