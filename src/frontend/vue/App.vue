<template>
    <div id="app">
        <sidebar-pane></sidebar-pane>
		<main-pane></main-pane>
    </div>
</template>

<script>
import SidebarPane from "./Sidebar/SidebarPane.vue";
import MainPane from "./Main/MainPane.vue";

module.exports = {
	components: {
		SidebarPane,
		MainPane
	}
}
</script>

<style>
/* Universal */
*, *:before, *:after {
	box-sizing: border-box;
	margin: 0;
	padding: 0;
}

/* Body */
html, body {
	height: 100%;
}

html {
	font-family: 'Open Sans', sans-serif;
	background: #222;
}

body {
	position: relative;
	margin: 0 auto;
	color: white;
}

/* Header*/

/* Sidebar */

/* Content */

/* Horizontal Timeline Styles */

/* Vertical Timeline Styles */

/* Search Styles */

/* Footer Styles */

/* Error and Success Styles */

/* Break YouTube videos to 1 column */

/* Breakpoint */

/* Break YouTube videos to 3 columns */
</style>