<template>
	<div class="session-wrapper">
		<div class="session-left">
			<session-slider-widget></session-slider-widget>
		</div>
		<div class="session-right text-xs-center">
			<div class="session-table-cell">
				<div class="session-content">
					<img 
						:src="appLogo"
						class="img-responsive mb-3" 
						width="78" 
						height="78" 
					/>
					<h2 class="mb-3">{{$t('message.loginToAdmin')}}</h2>
					<p class="fs-14">{{$t('message.enterUsernameAndPasswordToAccessControlPanelOf')}} {{brand}}.</p>
					<v-form v-model="valid" class="mb-4">
						<v-text-field 
							label="E-mail ID" 
							v-model="email" 
							:rules="emailRules" 
							required
						></v-text-field>
						<v-text-field 
							label="Password" 
							v-model="password" 
							type="password" 
							:rules="passwordRules" 
							required
						></v-text-field>
						<v-checkbox 
							color="primary" 
							label="Remember me" 
							v-model="checkbox"
						></v-checkbox>
						<router-link class="mb-1" to="/session/forgot-password">{{$t('message.forgotPassword')}}?</router-link>
						<div>
							<v-btn large @click="submit" block color="primary">{{$t('message.loginNow')}}</v-btn>
							<v-btn large @click="signInWithLaravelPassport" block color="primary">{{$t('message.loginWithLaravelPassport')}}</v-btn>
							<v-btn large @click="onCreateAccount" block color="warning">{{$t('message.createAccount')}}</v-btn>
						</div>
						<p>{{$t('message.bySigningUpYouAgreeTo')}} {{brand}}</p>
						<router-link to="">{{$t('message.termsOfService')}}</router-link>
					</v-form>
					<div class="session-social-links d-inline-block">
						<ul class="list-inline">
							<li @click="signInWithFacebook">
								<span class="facebook-bg session-icon">
									<i class="ti-facebook"></i>
								</span>
							</li>
							<li @click="signInWithGoogle">
								<span class="google-bg session-icon">
									<i class="ti-google"></i>
								</span>
							</li>
							<li @click="signInWithTwitter">
								<span class="twitter-bg session-icon">
									<i class="ti-twitter-alt"></i>
								</span>
							</li>
							<li @click="signInWithGithub">
								<span class="github-bg session-icon">
									<i class="ti-github"></i>
								</span>
							</li>
						</ul>
						<v-btn 
							color="error" 
							@click="signinWithAuth0"
						>
							Signin With Auth0
						</v-btn>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { mapGetters } from "vuex";
import SessionSliderWidget from "Components/Widgets/SessionSlider";
import AppConfig from "Constants/AppConfig";

import AuthService from "../../auth/AuthService";

const auth = new AuthService();
const { login, logout, authenticated, authNotifier } = auth;

export default {
  components: {
    SessionSliderWidget
  },
  data() {
    return {
      checkbox: false,
      valid: false,
      email: "demo@example.com",
      emailRules: [
        v => !!v || "E-mail is required",
        v =>
          /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(v) ||
          "E-mail must be valid"
      ],
      password: "test#123",
      passwordRules: [v => !!v || "Password is required"],
      appLogo: AppConfig.appLogo2,
      brand: AppConfig.brand
    };
  },
  methods: {
    submit() {
      const user = {
        email: this.email,
        password: this.password
      };
      this.$store.dispatch("signinUserInFirebase", {
        user
      });
    },
		signInWithLaravelPassport(){
			 const user = {
        email: this.email,
        password: this.password
      };
      this.$store.dispatch("signInWithLaravelPassport", {
        user
      });
		},
    signInWithFacebook() {
      this.$store.dispatch("signinUserWithFacebook");
    },
    signInWithGoogle() {
      this.$store.dispatch("signinUserWithGoogle");
    },
    signInWithTwitter() {
      this.$store.dispatch("signinUserWithTwitter");
    },
    signInWithGithub() {
      this.$store.dispatch("signinUserWithGithub");
    },
    onCreateAccount() {
      this.$router.push("/session/sign-up");
    },
    signinWithAuth0() {
      login();
    }
  }
};
</script>
