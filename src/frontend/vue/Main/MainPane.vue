<template>
    <section :class="appClass">
        <header :class="$style.mainHeader">
            <span :class="$style.toggleSidebarButton" class="icon-reorder" @click="toggleSidebar"></span>
            <h1 :class="$style.mainTitle">Painkiller Already</h1>
			<button v-if="$store.state.loggedIn" @click.prevent="logout" :class="$style.logoutButton">Log Out</button>
        </header>
        <div :class="$style.appContainer">
            <div v-for="success in successes" :class="$style.successMessage">
                <p v-text="success"></p>
            </div>
            <div v-for="error in errors" :class="$style.errorMessage">
                <p v-text="error"></p>
            </div>
            <router-view></router-view>
            <ul :class="$style.footerLinkList">
                <li :class="$style.footerLinkItem"><router-link :class="$style.footerLink" to="/credits">Developers and Contributors</router-link></li>
                <li :class="$style.footerLinkItem"><router-link :class="$style.footerLink" to="/feedback">Provide us with Feedback</router-link></li>
            </ul>
        </div>
    </section>
</template>

<script>
module.exports = {
	computed: {
		errors: function() {
			return this.$store.state.globalErrors;
		},
		successes: function() {
			return this.$store.state.globalSuccesses;
		},
		appClass: function() {
			return {
				[this.$style.main]: true,
				[this.$style.mainWhenSidebarOpen]: this.$store.state.sidebarOpen,
				[this.$style.mainWhenSidebarClosed]: !this.$store.state.sidebarOpen
			};
		}
	},
	methods: {
		toggleSidebar: function() {
			this.$store.dispatch("toggleSidebar");
		},
		logout: function() {
			this.$store.dispatch("clearErrors");
			this.$store.dispatch("logout");
		}
	}
}
</script>

<style module>
.message {
	border: 1px solid;
	margin: 0 0 20px;
	padding: 10px 10px 10px 50px;
	background-repeat: no-repeat;
	background-position: 10px center;
}

.successMessage {
	composes: message;
	color: #4F8A10;
	background-color: #DFF2BF;
	background-image: url("../../img/success.png");
}

.errorMessage {
	composes: message;
	color: #D8000C;
	background-color: #FFBABA;
	background-image: url("../../img/error.png");
}

.main {
	composes: pane from "./../Global.css";
	background: #414040;
	position: absolute;
	left: 230px;
	right: 0;
}

.mainWhenSidebarOpen {
	composes: paneWhenSidebarOpen from "./../Global.css"
}

.mainWhenSidebarClosed {
	composes: paneWhenSidebarClosed from "./../Global.css"
}

.mainHeader {
	background: #BB4D3E;
	overflow: hidden;
	border-bottom: 7px solid #D97062;
	white-space: nowrap;
}

.mainTitle {
	font-weight: bold;
	font-size: 2em;
	padding: 0 20px;
	line-height: 2;
	position: relative;
	overflow: hidden;
	-o-text-overflow: ellipsis;
	text-overflow: ellipsis;
	float: left;
}

.logoutButton {
	composes: loginButton from "./../Global.css";
	float: right;
	margin: 12px; /*15px*/
	padding: 10px;
	background: #9c463a;
}

.appContainer {
	padding: 20px;
}

.toggleSidebarButton {
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

.toggleSidebarButton:hover {
	color: white;
}

.toggleSidebarButton:before {
	height: 64px;
	width: 64px;
	display: block;
	text-align: center;
	line-height: 64px;
}

.footerLinkList {
	margin: 10px 0 -5px 0;
	text-align: center;
}

.footerLinkItem {
	display: inline;
	list-style: none;
	margin: 0 10px;
}

.footerLink {
	color: #666;
	text-decoration: none;
	font-size: 0.8em;
}

.footerLink:hover {
	color: #777;
}

@media (max-width: 1024px) {
	/* Content */
	.main {
		right: -230px;
	}
	.mainTitle {
		left: 40px;
	}
	.toggleSidebarButton {
		z-index: 1;
	}
	.footerLinkItem {
		display: block;
	}
}
</style>