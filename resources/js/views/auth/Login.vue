<template>
  <div class="background">
    <div class="wrapper-page">
      <div class="login-card overflow-hidden account-card mx-3">
        <div class="bg-gradient p-4 text-white text-center position-relative">
          <h4 class="font-20 m-b-5">Welcome</h4>
          <p class="text-white-50 mb-4" style="font-weight: 600">{{ projectName() }}</p>
        </div>
        <div class="account-card-content">
          <ValidationObserver v-slot="{ handleSubmit }">
            <form class="form-horizontal m-t-30" @submit.prevent="handleSubmit(onSubmit)">
              <ValidationProvider name="User ID" mode="eager" rules="required" v-slot="{ errors }">
                <div class="form-group">
                  <label for="username">User ID</label>
                  <input type="text" class="form-control" :class="{'error-border': errors[0]}" id="username"
                         v-model="username" name="username" placeholder="User ID" autocomplete="off">
                  <span class="error-message"> {{ errors[0] }}</span>
                </div>
              </ValidationProvider>
              <ValidationProvider name="Password" mode="eager" rules="required|min:4" v-slot="{ errors }">
                <div class="form-group">
                  <label for="user-password">Password</label>
                  <input type="password" v-model="password" class="form-control" :class="{'error-border': errors[0]}"
                         id="user-password" placeholder="Password" autocomplete>
                  <span class="error-message">{{ errors[0] }}</span>
                </div>
              </ValidationProvider>
              <submit-form name="Log In"/>
            </form>
          </ValidationObserver>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import {Common} from '../../mixins/common'
import moment from "moment";
import {projectName} from "../../base_url";

export default {
  mixins: [Common],
  data() {
    return {
      username: '',
      password: '',

    }
  },
  computed: {
    now() {
      return moment()
    }
  },
  mounted() {
    // this.getData();
  },
  methods: {
    projectName() {
      return projectName
    },

    onSubmit() {
      this.$store.commit('submitButtonLoadingStatus', true);
      this.axiosPostWithoutToken('login', {
        username: this.username,
        password: this.password
      }, (response) => {
        localStorage.setItem("token", response.access_token);
          console.log(response)
        this.successNoti('Successfully logged in.');
        this.$store.commit('submitButtonLoadingStatus', false);
        this.redirect(this.mainOrigin + 'dashboard')

      }, (error) => {
        this.errorNoti(error);
        this.$store.commit('submitButtonLoadingStatus', false);
      })
    }
  }
}
</script>
