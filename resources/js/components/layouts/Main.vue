<template>
  <div id="layout-wrapper">
    <Sidebar/>
    <div class="main-content">
      <div class="page-content">
        <router-view></router-view>
        <Footer/>
      </div>
    </div>
  </div>
</template>
<script>
import Sidebar from './Sidebar'
import Footer from './Footer'
import Navbar from "./Navbar.vue";

export default {
  components: {
    Navbar,
    Sidebar,
    Footer
  }
}
</script>
