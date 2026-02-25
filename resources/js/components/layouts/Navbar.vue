<template>
  <header id="page-topbar">
    <div class="navbar-header">
      <div class="d-flex">
        <!-- LOGO -->
        <div class="navbar-brand-box">
          <a href="index.html" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="assets/images/logo-sm.png" alt="" height="22">
                                </span>
            <span class="logo-lg">
                                    <img src="assets/images/logo-dark.png" alt="" height="17">
                                </span>
          </a>

          <a href="index.html" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="assets/images/logo-sm.png" alt="" height="22">
                                </span>
            <span class="logo-lg">
                                    <img src="assets/images/logo-light.png" alt="" height="18">
                                </span>
          </a>
        </div>

        <button type="button"
                class="btn btn-sm me-2 font-size-24 d-lg-none header-item waves-effect waves-light"
                data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
          <i class="mdi mdi-menu"></i>
        </button>

      </div>

      <div class="d-flex">

        <!-- App Search-->
        <form class="app-search d-none d-lg-block">
          <div class="position-relative">
            <input type="text" class="form-control" placeholder="Search...">
            <span class="fa fa-search"></span>
          </div>
        </form>

        <div class="dropdown d-none d-md-block ms-2">
          <button type="button" class="btn header-item waves-effect" data-bs-toggle="dropdown"
                  aria-haspopup="true" aria-expanded="false">
            <img class="me-2" src="assets/images/flags/us_flag.jpg" alt="Header Language" height="16">
            English
            <span class="mdi mdi-chevron-down"></span>
          </button>
          <div class="dropdown-menu dropdown-menu-end">

            <!-- item-->
            <a href="javascript:void(0);" class="dropdown-item notify-item">
              <img src="assets/images/flags/germany_flag.jpg" alt="user-image" class="me-1" height="12">
              <span class="align-middle"> German </span>
            </a>

            <!-- item-->
            <a href="javascript:void(0);" class="dropdown-item notify-item">
              <img src="assets/images/flags/italy_flag.jpg" alt="user-image" class="me-1" height="12">
              <span class="align-middle"> Italian </span>
            </a>

            <!-- item-->
            <a href="javascript:void(0);" class="dropdown-item notify-item">
              <img src="assets/images/flags/french_flag.jpg" alt="user-image" class="me-1" height="12">
              <span class="align-middle"> French </span>
            </a>

            <!-- item-->
            <a href="javascript:void(0);" class="dropdown-item notify-item">
              <img src="assets/images/flags/spain_flag.jpg" alt="user-image" class="me-1" height="12">
              <span class="align-middle"> Spanish </span>
            </a>

            <!-- item-->
            <a href="javascript:void(0);" class="dropdown-item notify-item">
              <img src="assets/images/flags/russia_flag.jpg" alt="user-image" class="me-1" height="12">
              <span class="align-middle"> Russian </span>
            </a>
          </div>
        </div>

        <div class="dropdown d-none d-lg-inline-block">
          <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="fullscreen">
            <i class="mdi mdi-fullscreen"></i>
          </button>
        </div>

        <div class="dropdown d-inline-block">
          <button type="button" class="btn header-item noti-icon waves-effect"
                  id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                  aria-expanded="false">
            <i class="mdi mdi-bell-outline"></i>
            <span class="badge bg-danger rounded-pill">3</span>
          </button>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
               aria-labelledby="page-header-notifications-dropdown">
            <div class="p-3">
              <div class="row align-items-center">
                <div class="col">
                  <h5 class="m-0 font-size-16"> Notifications (258) </h5>
                </div>
              </div>
            </div>
            <div data-simplebar="init" style="max-height: 230px;">
              <div class="simplebar-wrapper" style="margin: 0px;">
                <div class="simplebar-height-auto-observer-wrapper">
                  <div class="simplebar-height-auto-observer"></div>
                </div>
                <div class="simplebar-mask">
                  <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                    <div class="simplebar-content-wrapper" style="height: auto; overflow: hidden;">
                      <div class="simplebar-content" style="padding: 0px;">
                        <a href="" class="text-reset notification-item">
                          <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                              <div class="avatar-xs">
                                                    <span class="avatar-title bg-success rounded-circle font-size-16">
                                                        <i class="mdi mdi-cart-outline"></i>
                                                    </span>
                              </div>
                            </div>
                            <div class="flex-grow-1">
                              <h6 class="mb-1">Your order is placed</h6>
                              <div class="font-size-12 text-muted">
                                <p class="mb-1">Dummy text of the printing and
                                  typesetting.</p>
                              </div>
                            </div>
                          </div>
                        </a>

                        <a href="" class="text-reset notification-item">
                          <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                              <div class="avatar-xs">
                                                    <span class="avatar-title bg-warning rounded-circle font-size-16">
                                                        <i class="mdi mdi-message-text-outline"></i>
                                                    </span>
                              </div>
                            </div>
                            <div class="flex-grow-1">
                              <h6 class="mb-1">New Message received</h6>
                              <div class="font-size-12 text-muted">
                                <p class="mb-1">You have 87 unread messages</p>
                              </div>
                            </div>
                          </div>
                        </a>

                        <a href="" class="text-reset notification-item">
                          <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                              <div class="avatar-xs">
                                                    <span class="avatar-title bg-info rounded-circle font-size-16">
                                                        <i class="mdi mdi-glass-cocktail"></i>
                                                    </span>
                              </div>
                            </div>
                            <div class="flex-grow-1">
                              <h6 class="mb-1">Your item is shipped</h6>
                              <div class="font-size-12 text-muted">
                                <p class="mb-1">It is a long established fact that a
                                  reader will</p>
                              </div>
                            </div>
                          </div>
                        </a>

                        <a href="" class="text-reset notification-item">
                          <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                              <div class="avatar-xs">
                                                    <span class="avatar-title bg-primary rounded-circle font-size-16">
                                                        <i class="mdi mdi-cart-outline"></i>
                                                    </span>
                              </div>
                            </div>
                            <div class="flex-grow-1">
                              <h6 class="mb-1">Your order is placed</h6>
                              <div class="font-size-12 text-muted">
                                <p class="mb-1">Dummy text of the printing and
                                  typesetting industry.</p>
                              </div>
                            </div>
                          </div>
                        </a>

                        <a href="" class="text-reset notification-item">
                          <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                              <div class="avatar-xs">
                                                    <span class="avatar-title bg-danger rounded-circle font-size-16">
                                                        <i class="mdi mdi-message-text-outline"></i>
                                                    </span>
                              </div>
                            </div>
                            <div class="flex-grow-1">
                              <h6 class="mb-1">New Message received</h6>
                              <div class="font-size-12 text-muted">
                                <p class="mb-1">You have 87 unread messages</p>
                              </div>
                            </div>
                          </div>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="simplebar-placeholder" style="width: 0px; height: 0px;"></div>
              </div>
              <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                <div class="simplebar-scrollbar"
                     style="transform: translate3d(0px, 0px, 0px); display: none;"></div>
              </div>
              <div class="simplebar-track simplebar-vertical" style="visibility: hidden;">
                <div class="simplebar-scrollbar"
                     style="transform: translate3d(0px, 0px, 0px); display: none;"></div>
              </div>
            </div>
            <div class="p-2 border-top">
              <div class="d-grid">
                <a class="btn btn-sm btn-link font-size-14 text-center" href="javascript:void(0)">
                  View all
                </a>
              </div>
            </div>
          </div>
        </div>

        <div class="dropdown d-inline-block">
          <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                  data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <img class="rounded-circle header-profile-user" src="assets/images/users/user-4.jpg"
                 alt="Header Avatar">
          </button>
          <div class="dropdown-menu dropdown-menu-end">
            <!-- item-->
            <a class="dropdown-item" href="#"><i
                class="mdi mdi-account-circle font-size-17 align-middle me-1"></i> Profile</a>
            <a class="dropdown-item" href="#"><i class="mdi mdi-wallet font-size-17 align-middle me-1"></i>
              My
              Wallet</a>
            <a class="dropdown-item d-block" href="#"><span class="badge bg-success float-end">11</span><i
                class="mdi mdi-cog font-size-17 align-middle me-1"></i> Settings</a>
            <a class="dropdown-item" href="#"><i
                class="mdi mdi-lock-open-outline font-size-17 align-middle me-1"></i> Lock screen</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item text-danger" href="#"><i
                class="bx bx-power-off font-size-17 align-middle me-1 text-danger"></i> Logout</a>
          </div>
        </div>

        <div class="dropdown d-inline-block">
          <button type="button" class="btn header-item noti-icon right-bar-toggle waves-effect">
            <i class="mdi mdi-cog-outline"></i>
          </button>
        </div>

      </div>
    </div>
  </header>
<!--  <div class="topbar">-->
<!--    <div class="topbar-left">-->
<!--      <router-link :to="{name:'Dashboard'}" class="logo">-->
<!--        <span><img style="width: 70%;height: 75px" :src="`${mainOrigin}assets/images/logo.svg`"-->
<!--                   alt="logo"/> </span><i><img-->
<!--          :src="`${mainOrigin}assets/images/favicon.ico`" style="height: 50px ;width: 100%" alt=""/></i>-->
<!--      </router-link>-->
<!--    </div>-->
<!--    <nav class="navbar-custom">-->
<!--      <ul class="navbar-right list-inline float-right mb-0">-->
<!--        <li class="dropdown notification-list list-inline-item">-->
<!--          <div class="dropdown notification-list nav-pro-img">-->
<!--            <a class="dropdown-toggle nav-link arrow-none waves-effect nav-user" data-toggle="dropdown"-->
<!--               href="#" role="button" aria-haspopup="false" aria-expanded="false">-->
<!--              <img :src="image" alt="user"-->
<!--                   class="rounded-circle"/>-->
<!--            </a>-->
<!--            <div class="dropdown-menu dropdown-menu-right profile-dropdown">-->
<!--              <a class="dropdown-item text-danger" @click="logout" href="#"><i-->
<!--                  class="mdi mdi-power text-danger"></i>-->
<!--                Logout</a>-->
<!--            </div>-->
<!--          </div>-->
<!--        </li>-->
<!--      </ul>-->
<!--      <ul class="list-inline menu-left mb-0">-->
<!--        <li class="float-left">-->
<!--          <button @click="toggleSidebar" class="button-menu-mobile open-left waves-effect"><i-->
<!--              class="mdi mdi-menu"></i></button>-->
<!--        </li>-->
<!--      </ul>-->
<!--    </nav>-->
<!--  </div>-->
</template>
<script>
import {Common} from '../../mixins/common'

export default {
  mixins: [Common],
  data() {
    return {
      image: ''
    }
  },
  created() {
    this.getData();
  },
  methods: {
    toggleSidebar(e) {
      $("body").toggleClass("enlarged")
    },
    getData() {
      this.axiosPost('me', {}, (response) => {
        this.image = `${this.mainOrigin}assets/images/avatar.png`;
        this.$store.commit('me', response);
      }, (error) => {
        this.errorNoti(error);
      });
    },
    logout() {
      this.axiosPost("logout", {}, (response) => {
            localStorage.setItem("token", "");
            this.$router.push(this.mainOrigin + "login");
            this.successNoti(response.message)
          },
          (error) => {
            this.errorNoti(error);
          }
      );

    }
  }
}
</script>
