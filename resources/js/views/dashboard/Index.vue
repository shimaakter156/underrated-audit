<template>
  <div class="container-fluid">
    <div class="page-title-box">
      <div class="row align-items-center">
        <div class="col-sm-6">
          <h4 class="page-title">Dashboard</h4>
        </div>
      </div>
    </div>
    <!-- end row -->
    <div class="row">
      <div class="col-xl-12 col-md-12">
        <div class="card mini-stat bg-gradient-stable text-white header-bg">
          <div class="card-body" v-if="!isLoading">
            <span style="letter-spacing:5px;">Welcome to  Advance Management System (AMS)</span>
          </div>
          <div class="card-body" v-else>
            <skeleton-loader :row="4"/>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xl-8 col-md-8">
        <div class="card mini-stat">
          <div class="card-body">
            <div class="task">
              <span>Use Requisition menu for creating advance requisition.</span>
            </div>
            <div class="task">
              <span>Use Adjustment menu for submitting advance adjustment.</span>
            </div>
            <div class="task">
              <span>Use Reports menu for viewing report of unadjusted/adjusted advances.</span>
            </div>
            <div class="task">
              <span>Please change your password on first log in.</span>
            </div>
          </div>
        </div>
      </div>
      <div v-if="me.RoleID==='SuperAdmin'" class="col-xl-4 col-md-4">
        <div class="card mini-stat">
          <div class="card-body">
            <div class="links">
              <router-link :to="{name:'Branches'}" class="btn btn-primary" >Add Branch</router-link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-xl-4 col-md-4">
        <div class="card mini-stat">
          <div class="card-body">
            <div class="helpline">
              <span><i class="ti-call"></i> Help Line:</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-4 col-md-4">
        <div class="card mini-stat">
          <div class="card-body">
            <div class="contact">
              <p>Md. Anwarul Islam</p>
              <p>Phone: 01714 163038</p>
              <p>Email: anwarul@aci-bd.com</p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-4 col-md-4">
        <div class="card mini-stat">
          <div class="card-body">
            <div class="contact">
              <p>Md. Shazzadul Islam</p>
              <p>Phone: 01713-053236</p>
              <p>Email: shazzadul@aci-bd.com</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import {Common} from "../../mixins/common";

export default {
  mixins: [Common],
  data() {
    return {
      isLoading: true
    }
  },
  computed: {
    me() {
      return this.$store.state.me
    }
  },
  created() {
    this.getData();
  },
  methods: {
    getData() {
      // this.axiosGet('dashboard-data', (response) => {
      //    this.total_pending = response.total_pending;
      //    this.todays_order = response.todays_order;
      //    this.todays_amount = response.todays_amount;
      //    this.total_order = response.total_order;
      this.isLoading = false;
      // }, (error) => {
      //   this.errorNoti(error);
      // });
    }
  }
}
</script>


<style scoped>
.bg-blue {
  background: #626ed4!important;
  text-align: center;
  text-transform: uppercase;
}
.card-body {
  padding: 0.60rem !important;
}
.bg-blue span {
  font-size: 18px;
}
.task {
  background: #00a55d2b;
  padding: 5px 8px;
  font-size: 14px;
  margin-bottom: 10px;
  border-radius: 5px;
  font-weight: bold;
}
.links {
  /*height: 148px;*/
}
.helpline {
  height: 120px;
  text-align: center;
  position: relative;
  background-image: linear-gradient(146deg,#626ed4,#626ed4);
}
.helpline span {
  position: absolute;
  top: 40%;
  left: 0;
  right: 0;
  font-weight: bold;
  font-size: 20px;
  text-transform: uppercase;
  color: #FFFFFF;
}
.contact {
  height: 120px;
  text-align: center;
  padding-top: 20px;
  background-image: linear-gradient(146deg,#626ed4,#626ed4);
}
.contact p {
  margin: 0;
  padding: 2px;
  font-weight: bold;
  color: #ffffff;
}
.header-bg {
  text-align: center;
  font-size: 16px;
  font-weight: bold;
  text-transform: uppercase;
}
</style>
