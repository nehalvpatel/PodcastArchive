<template>
    <div id="app">
        <sidebar :episodes="episodes"></sidebar>
        <section class="main">
            <header>
                <span class="toggle-menu icon-reorder" @click="toggleSidebar"></span>
                <h1>Painkiller Already</h1>
            </header>
            <div id="container">
				<router-view></router-view>
                <ul id="footer-links">
                    <li><router-link to="/credits" activeClass="active">Developers and Contributors</router-link></li>
                    <li><router-link to="/feedback" activeClass="active">Provide us with Feedback</router-link></li>
                </ul>
            </div>
        </section>
    </div>
</template>

<script>
module.exports = {
	computed: {
		episodes: function() {
			return this.$store.state.episodes;
		}
	},
	methods: {
		toggleSidebar: function() {
			this.$store.dispatch("toggleSidebar", this.$store.state.sidebarOpen);
		}
	}
}
</script>

<style>
/* Universal */
*, *:before, *:after {
	-moz-box-sizing: border-box;
	-webkit-box-sizing: border-box;
	box-sizing: border-box;
	margin: 0;
	padding: 0;
	border: 0;
	outline: 0;
}
.clear {
	clear: both;
}

/* Body */
html {
	font-family: 'Open Sans', sans-serif;
	background: #222;
}
html, body {
	height: 100%;
}
body {
	position: relative;
	margin: 0 auto;
	color: white;
}

/* Header*/
header {
	background: #BB4D3E;
	overflow: hidden;
	border-bottom: 7px solid #D97062;
	white-space: nowrap;
}
h1 {
	font-weight: bold;
	font-size: 2em;
	padding: 0 20px;
	line-height: 2;
	position: relative;
	overflow: hidden;
	-o-text-overflow: ellipsis;
	text-overflow: ellipsis;
}

/* Sidebar */
.sidebar, .main {
	overflow-x: hidden;
	overflow-y: scroll;
	-webkit-overflow-scrolling: touch;
	position: absolute;
	top: 0;
	bottom: 0;
	-webkit-transition: -webkit-transform 0.2s ease-out, right 0.2s ease-out;
	transition: transform 0.2s ease-out, right 0.2s ease-out;
}
.sidebar {
	width: 230px;
	background: #333;
	overflow: hidden;
}
h3 {
	background-color: #555555;
	color: #cccccc;
	padding: 0 20px;
	font-weight: normal;
	line-height: 2;
	text-transform: uppercase;
	font-size: 90%;
}
.random-button {
	display: inline-block;
	margin: 4px -8px 0 0 !important;
	padding: 0 !important;
	text-decoration: none;
}
.random-button i {
	font-size: 12px;
	vertical-align: top;
	padding: 4px;
}
.toggle-menu {
	text-decoration: none;
	float: left;
	color: white;
	display: block;
	position: absolute;
	left: 0;
	z-index: -1;
	font-size: 24px;
	cursor: pointer;
}
.toggle-menu:hover {
	color: white;
}
.toggle-menu:before {
	height: 64px;
	width: 64px;
	display: block;
	text-align: center;
	line-height: 64px;
}
nav ul li {
	background: #333;
	list-style: none;
	border-bottom: 1px solid #444;
	color: #aaa;
}
nav ul li a {
	text-decoration: none;
	display: block;
	font-weight: bold;
	color: inherit;
	padding: 10px 0 10px 25px;
	border-left: 10px solid transparent;
}
nav ul li a:hover {
	border-left: 10px solid #BB4D3E;
}
.timelined {
	background: #3a3a3a;
	color: #666;
	padding: 2px 3px;
	font-size: 0.6em;
	float: right;
	margin: 2px 12px 0 0;
}
.timelined:not(.random-button):before {
	content: "TIMELINED";
}
.router-link-active .timelined {
	background: #A0493E;
	color: #aaa;
}
.router-link-active {
	background: #BB4D3E;
	color: #FFFFFF;
}
.highlighted-episode {
	border-left: 10px solid #444;
}
#sidebar {
	height: 100%;
	overflow: auto;
}

/* Content */
.main {
	background: #414040;
	position: absolute;
	left: 230px;
	right: 0;
}
#container {
	padding: 20px 20px 20px 20px;
}
#page-title {
	padding: 10px;
	background: #555555;
}
#page-title p {
	margin-top: 5px;
}
.section {
	background: #BB4D3E;
	padding: 10px;
	margin-top: 20px;
}
.section-header {
	margin: 0 10px 10px 0;
}
.section h3 {
	margin: 0 -10px;
}
.section ul {
	margin: 10px 0 0;
	padding-left: 30px;
	list-style-type: square;
}
.section li a {
	color: white;
	font-weight: bold;
}
.item {
	display: inline-block;
	margin: 0 10px 10px 0;
	vertical-align: top;
	background: #333;
}
.items {
	padding: 10px 0 0 10px;
	float: left;
	margin-right: 20px;
}
.items img {
	vertical-align: top;
	max-width: 50px;
	padding: 4px;
}
#image {
	max-height: 100%;
	padding: 10px;
	background: #333;
	float: left;
	text-align: center;
}
#person-image {
	max-width: 100%;
	vertical-align: top;
}
.info small {
	margin-left: 5px;
}
.info i {
	font-size: 15px;
}
.comments, .author {
	text-decoration: none;
	color: inherit;
	margin-left: 10px;
}
#rock-hardplace {
	height: 20px;
}
#player {
	height: 400px;
	width: 100%;
	vertical-align: top;
	display: block;
}
#overview {
	margin-top: 0px;
}
#details {
	overflow: auto;
	padding-left: 20px;
}
#social-icons {
	float: none;
	margin-right: 0px;
}
#youtube-videos {
	padding: 10px 0px 0px 10px;
}
.video {
	display: block;
	float: left;
	width: -webkit-calc((100% / 6) - 10px - (100% * 0.00005));
	width: -moz-calc((100% / 6) - 10px - (100% * 0.00005));
	width: calc((100% / 6) - 10px - (100% * 0.00005));
	padding: 10px;
	overflow: hidden;
	color: white;
	background: #333;
	text-decoration: none;
	margin: 0 10px 10px 0px;
}
.video-thumbnail {
	width: 100%;
}
.video-title {
	display: block;
	font-size: 13.5pt;
	font-weight: bold;
}
.video-details {
	font-size: 10pt;
	margin-top: 5px;
}
.video-comments {
	float: right;
}
#loader {
	width: 100%;
	height: 100%;
	position: fixed;
	top: 0;
	left: 0;
	z-index: 999;
	background: rgba(0,0,0,.5);
}
.error {
	text-align:center;
	color: #777;
	text-shadow: 2px 2px 2px #333;
}
.error img {
	margin-bottom: 50px;
	max-width: 747px;
	width: 100%;
}
.error h2{
	font-size: 3em;
}
.error h3{
	background: transparent;
}
#search-error p {
	padding: 20px;
}
#search-error p::before {
	content: 'There was an error with the search engine.';
	white-space: pre-wrap;
}
form div {
	margin: 10px 0 0;
}
form label {
	-ms-word-break: break-all;
	word-break: break-all;
}
textarea {
	display: block;
	width: 100%;
	resize: vertical;
}
input[type=submit] {
	display: block;
	padding: 5px 7px;
	width: 100%;
	margin-top: 20px;
}

/* Horizontal Timeline Styles */
.timeline {
	height: 35px;
	-webkit-transition: margin-bottom 0.1s ease-out;
	transition: margin-bottom 0.1s ease-out;
}
.timeline:hover {
	margin-bottom: 35px;
}
.timeline a {
	color: inherit;
}
.topic {
	height: 100%;
	float: left;
	background: #333;
	border-right: 1px solid #414040;
	border-top: 4px solid #414040;
}
.topic:hover {
	background: #414040;
}
.topic:hover * {
	display: block;
}
.tooltip {
	width: 300px;
	padding: 3px;
	background: black;
	position: relative;
	top: 40px;
	text-align: center;
	left: -5px;
	display: none;
}
.tooltip .triangle {
	position: absolute;
	top: -10px;
	height: 0;
	width: 1px;
	border: 5px solid black;
	border-color: transparent transparent black transparent;
}
.tooltip span {
	font-size: 0.8em;
}
.right {
	left: -290px;
}
.right .triangle {
	left: 288px;
}
.active-timestamp-horizontal {
	background: #414040 !important;
}

/* Vertical Timeline Styles */
#timeline-vertical {
	width: 100%;
	padding: 0;
	border-collapse: collapse;
	border-spacing: 0;
}
#timeline-vertical thead {
	background: #BB4D3E;
}
#timeline-vertical tbody {
	background: #404040;
	font-size: 10.5pt;
}
#timeline-vertical tr {
	border-left: 4px solid #BB4D3E;
	border-right: 4px solid #BB4D3E;
}
#timeline-vertical th:first-child {
	border-color: #BB4D3E;
}
#timeline-vertical th {
	padding: 5px;
}
#timeline-vertical td {
	border: 4px solid #BB4D3E;
	padding: 5px 7px;
	vertical-align: top;
}
.active-timestamp-vertical {
	font-weight: bold;
	background: #333;
}
td.timestamp {
	width: 35px;
}
td.timestamp a {
	color: inherit;
}
td.event {
	-ms-word-break: break-word;
	word-break: break-word;
}
td.event a {
	color: inherit;
}

/* Search Styles */
.search-form {
	padding: 7px;
}
.search-field {
	width: 100%;
	border-style: none;
	padding: 5px 10px 5px 10px;
	background: #555;
}
.search-field:focus {
	background: #777;
}
.search-result {
	width: 175px;
	display: block;
	font-size: smaller;
	font-weight: normal;
}
input[type="search"] {
	-moz-appearance: textfield;
	-webkit-appearance: textfield;
}

/* Footer Styles */
#footer-links{
	margin: 10px 0 -5px 0;
	text-align: center;
}
#footer-links li{
	display: inline;
	list-style: none;
	margin: 0 10px;
}
#footer-links li a{
	color: #666;
	text-decoration: none;
	font-size: 0.8em;
}
#footer-links li a:hover{
	color: #777;
}

/* Error and Success Styles */
.success-message, .error-message {
	border: 1px solid;
	margin: 0 0 20px;
	padding: 10px 10px 10px 50px;
	background-repeat: no-repeat;
	background-position: 10px center;
}
.success-message {
	color: #4F8A10;
	background-color: #DFF2BF;
	background-image: url("../img/success.png");
}
.error-message {
	color: #D8000C;
	background-color: #FFBABA;
	background-image: url("../img/error.png");
}

/* Break YouTube videos to 1 column */
@media all and (max-width: 720px) {
	.video {
		width: -webkit-calc(100% - 10px - (100% * 0.00005)) !important;
		width: -moz-calc(100% - 10px - (100% * 0.00005)) !important;
		width: calc(100% - 10px - (100% * 0.00005)) !important;
	}
}

/* Breakpoint */
@media (max-width: 1024px) {
	/* Sidebar */
	.sidebar.toggled {
		-ms-transform: translateX(0px);
		-webkit-transform: translateX(0px);
		transform: translateX(0px);
	}
	.sidebar.toggled + .main {
		-ms-transform: translateX(0px);
		-webkit-transform: translateX(0px);
		transform: translateX(0px);
	}
	.sidebar {
		-ms-transform: translateX(-230px);
		-webkit-transform: translateX(-230px);
		transform: translateX(-230px);
	}
	.toggle-menu {
		z-index: 1;
	}
	
	/* Content */
	.main {
		-ms-transform: translateX(-230px);
		-webkit-transform: translateX(-230px);
		transform: translateX(-230px);
		right: -230px;
	}
	h1 {
		left: 40px;
	}
	.items {
		width: 100%;
		margin-right: 0;
	}
	.comments, .author {
		float: right;
	}
	#image, #details {
		width: 100%;
	}
	#image {
		text-align: center;
	}
	#details {
		padding: 20px 0px 0px 0px;
	}
	.video {
		width: -webkit-calc((100% / 2) - 10px - (100% * 0.00005));
		width: -moz-calc((100% / 2) - 10px - (100% * 0.00005));
		width: calc((100% / 2) - 10px - (100% * 0.00005));
	}
	.video:nth-of-type(odd) {
		clear: both;
	}
	.video-thumbnail {
		width: 100%;
		max-height: 200px;
	}
	#footer-links li {
		display: block;
	}
	
	/* Horizontal Timeline Styles */
	#timeline-horizontal {
		display: none;
	}
}

/* Break YouTube videos to 3 columns */
@media (min-width: 1025px) and (max-width: 1400px) {
	.video {
		width: -webkit-calc((100% / 3) - 10px - (100% * 0.00005));
		width: -moz-calc((100% / 3) - 10px - (100% * 0.00005));
		width: calc((100% / 3) - 10px - (100% * 0.00005));
	}
	.video:nth-of-type(3n + 1) {
		clear: both;
	}
}

@media (min-width: 1401px) {
	.video:nth-of-type(6n + 1) {
		clear: both;
	}
}
</style>