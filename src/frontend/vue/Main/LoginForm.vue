<template>
    <div v-if="$store.state.loggedIn === false">
        <form @submit.prevent="loginButtonClick" method="POST" :class="$style.loginForm">
            <input v-show="showLoginForm" :class="$style.loginField" type="text" v-model="formLoginUsername" placeholder="Username" />
            <input v-show="showLoginForm" :class="$style.loginField" type="password" v-model="formLoginPassword" placeholder="Password" />
            <button :class="$style.loginButton" type="submit">Log In</button>
        </form>
    </div>
    <div v-else>
        <button @click.prevent="logout" :class="$style.loginButton">Log Out</button>
    </div>
</template>

<script>
module.exports = {
    data: function() {
		return {
			showLoginForm: false,
			formLoginUsername: "",
			formLoginPassword: ""
		}
	},
    methods: {
        loginButtonClick: function() {
			if (this.showLoginForm === false) {
				this.showLoginForm = true;
			} else {
				if (this.formLoginUsername && this.formLoginPassword) {
					this.$store.dispatch("login", {
						Username: this.formLoginUsername,
						Password: this.formLoginPassword
					}).then((messages) => {
						this.$store.dispatch("displaySuccesses", messages);

						this.formLoginUsername = "";
						this.formLoginPassword = "";
						this.showLoginForm = false;
					}).catch((messages) => {
						this.$store.dispatch("displayErrors", messages);
					});
				} else {
					this.$store.dispatch("displayErrors", ["Please make sure you filled in the username and password."]);
				}
			}
		},
		logout: function() {
			this.$store.dispatch("clearErrors");
			this.$store.dispatch("logout");
		}
    }
}
</script>

<style module>
.loginForm {
	float: right;
}

.loginField {
	margin: 12px; /*15px*/
	height: 40px;
	padding: 10px;
	background: #9c463a;
	color: white;
	font-weight: bold;
	border: none;
}

.loginButton {
	float: right;
	margin: 12px; /*15px*/
	height: 40px;
	padding: 10px;
	background: #9c463a;
	color: white;
	font-weight: bold;
	width: auto;
	display: inline;
	border: none;
}
</style>