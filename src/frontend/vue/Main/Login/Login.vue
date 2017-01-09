<template>
    <div v-if="$store.state.loggedIn">
        <div :class="$style.pageTitle">
            <h2>Login</h2>
            <p :class="$style.pageDescription">You are logged in.</p>
        </div>
    </div>
    <div v-else>
        <div :class="$style.pageTitle">
            <h2>Login</h2>
        </div>
        <form @submit.prevent="login">
            <div :class="$style.section">
                <h3 :class="$style.sectionTitle">Username</h3>
                <input type="text" :class="$style.loginField" v-model="username" placeholder="Username">
            </div>
            <div :class="$style.section">
                <h3 :class="$style.sectionTitle">Password</h3>
                <input type="password" :class="$style.loginField" v-model="password" placeholder="Password">
            </div>
            <button type="submit" :class="$style.loginButton">Login</button>
        </form>
    </div>
</template>

<script>
module.exports = {
    metaInfo: function() {
        return {
            title: "Login",
            titleTemplate: "%s · Painkiller Already",
        };
    },
    created: function() {
        this.$store.dispatch("markLaunched");
    },
    data: function() {
        return {
            username: "",
            password: ""
        }
    },
    methods: {
        login: function() {
            if (this.username && this.password) {
                this.$store.dispatch("login", {
                    Username: this.username,
                    Password: this.password
                }).then((messages) => {
                    this.$store.dispatch("displaySuccesses", messages);

                    this.username = "";
                    this.password = "";
                }).catch((messages) => {
                    this.$store.dispatch("displayErrors", messages);
                });
            } else {
                this.$store.dispatch("displayErrors", ["Please make sure you filled in the username and password."]);
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

    .loginField {
        margin: 10px 0 0;
        width: 100%;
        height: 40px;
        padding: 10px;
        background: #9c463a;
        color: white;
        font-weight: bold;
        border: none;
    }

    .loginButton {
        composes: section from "./../../Global.css", loginButton from "./../../Global.css";
        height: 40px;
        color: white;
        font-weight: bold;
        width: 100%;
        display: inline;
        border: none;
    }

    .loginButton:hover {
        background: #9c463a;
    }
</style>