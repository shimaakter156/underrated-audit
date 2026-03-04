<template>
  <div>
    <div class="modal fade" id="add-edit-dept" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <div class="modal-title modal-title-font" id="exampleModalLabel">{{ title }}</div>
          </div>
          <ValidationObserver v-slot="{ handleSubmit }">
            <form class="form-horizontal" id="form" @submit.prevent="handleSubmit(onSubmit)">
              <div class="modal-body">
                <div class="row">
                  <div class="col-12 col-md-6">
                    <ValidationProvider name="Staff ID" mode="eager" rules="required" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="name">Staff ID <span class="error">*</span></label>
                        <input type="text" class="form-control" :class="{'error-border': errors[0]}"
                               v-model="staffId" placeholder="Staff ID" :disabled="actionType==='edit'" autocomplete="off">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-6">
                    <ValidationProvider name="Staff Name" mode="eager" rules="required" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="name">Staff Name <span class="error">*</span></label>
                        <input type="text" class="form-control" :class="{'error-border': errors[0]}" id="name"
                               v-model="staffName" name="staff-name" placeholder="Staff Name"
                               :disabled="actionType==='edit'">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-6">
                    <ValidationProvider name="email" mode="eager" rules="required" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="email">Email <span class="error">*</span></label>
                        <input type="text" class="form-control" :class="{'error-border': errors[0]}"
                               v-model="email" placeholder="Email">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-6">
                    <ValidationProvider name="mobile" mode="eager" rules="required|min:11|max:11" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="mobile">Mobile <span class="error">*</span></label>
                        <input type="text" class="form-control" :class="{'error-border': errors[0]}"
                               v-model="mobile" placeholder="Mobile">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-6">
                    <ValidationProvider name="User Type" mode="eager" rules="required" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="user-type">User Type <span class="error">*</span></label>
                        <multiselect v-model="userType" :options="userTypes" :multiple="false" :close-on-select="true"
                                     :clear-on-select="false" :preserve-search="true" placeholder="Select User type"
                                     label="UserTypeName" track-by="UserTypeID" >

                        </multiselect>
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-6">
                    <ValidationProvider name="Status" mode="eager" rules="required" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="status">Status <span class="error">*</span></label>
                        <select class="form-control" v-model="status">
                          <option value="1">Active</option>
                          <option value="0">Inactive</option>
                        </select>
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-6" v-if="actionType === 'add'">
                    <ValidationProvider name="password" mode="eager" rules="required|min:6"
                                        v-slot="{ errors }">
                      <div class="form-group">
                        <label for="name">Password <span class="error">*</span></label>
                        <input type="password" class="form-control" :class="{'error-border': errors[0]}" id="password"
                               v-model="password" name="password" placeholder="Password" autocomplete="off">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-6" v-if="actionType === 'add'">
                    <ValidationProvider name="confirm" mode="eager" rules="required|min:6|confirmed:password"
                                        v-slot="{ errors }">
                      <div class="form-group">
                        <label for="confirm">Confirm <span class="error">*</span></label>
                        <input type="password" class="form-control" :class="{'error-border': errors[0]}" id="confirm"
                               v-model="confirm"
                               name="confirm" placeholder="Confirm Password">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  
                  <div class="col-12">
                    <p class="font-weight-bold">Submenu Permission</p>
                  </div>
                  <div class="col-12 col-md-6" v-for="(submenu,index) in allSubMenu" :key="index">
                      <div class="form-group">
                        <div class="form-check">
                          <p> <u>{{submenu.MenuName}}</u></p>
                          <div v-for="(sub,index2) in submenu.all_sub_menus" :key="index2">
                            <input class="form-check-input" type="checkbox" :value="sub.SubMenuID" v-model="allSubMenuId" :id="'allSubMenu'+index">
                            <label class="form-check-label" :for="'allSubMenu'+index+'-'+index2">
                              {{sub.SubMenuName}}
                            </label>
                          </div>
                        </div>
                      </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <submit-form v-if="buttonShow" :name="buttonText"/>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              </div>
            </form>
          </ValidationObserver>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import {bus} from "../../app";
import {Common} from "../../mixins/common";
import {mapGetters} from "vuex";
import ImageUpload from "./ImageUpload.vue";

export default {
  mixins: [Common],
  data() {
    return {
      title: '',
      staffId: '',
      staffName: '',
      buttonText: '',
      mobile: '',
      email: '',
      status: '',
      password: '',
      confirm: '',
      outlet: '',
      userType: '',
      type: 'add',
      actionType: '',
      buttonShow: false,
      userTypes: [],
      allSubMenu: [],
      filteredSubMenu: [],
      allSubMenuId: [],
    }
  },
  computed: {},
  mounted() {
    $('#add-edit-dept').on('hidden.bs.modal', () => {
      this.$emit('changeStatus')
    });
    bus.$on('add-edit-user', (row) => {
      if (row) {
        this.selectedBusiness = [];
        this.selectedDepartment = [];
        let instance = this;
        this.axiosGet('user/get-user-info/'+row.UserID,function(response) {
          var user = response.data;
          instance.title = 'Update User';
          instance.buttonText = "Update";
          instance.staffName = user.Name;
          instance.staffId = user.UserID;
          instance.mobile = user.PhoneNo;
          instance.email = user.Email;
          instance.status = user.Status;
          instance.userType = {
            UserTypeName: user.user_type.UserTypeName,
            UserTypeID: user.user_type.UserTypeID
          };
          instance.buttonShow = true;
          instance.actionType = 'edit';
          instance.getData();
        },function(error){

        });
      } else {
        this.title = 'Add User';
        this.buttonText = "Add";
        this.buttonShow = true;
        this.staffId = '';
        this.staffName = '';
        this.mobile = '';
        this.email = '';
        this.status = '1';
        this.password = null;
        this.userType = '';
        this.allSubMenu = [];
        this.actionType = 'add'
        this.getData();
      }
      $("#add-edit-dept").modal("toggle");
      // $(".error-message").html("");
    })
  },
  destroyed() {
    bus.$off('add-edit-user')
    bus.$off('backToModal')
  },
  methods: {
    getData() {
      let instance = this;
      this.axiosGet('user/modal',function (response) {
        instance.userTypes = response.userTypes;
        instance.allSubMenu = response.allSubMenus;
        instance.filteredSubMenu = instance.checkSubMenus();
      },function (error) {

      });
    },
    onSubmit() {
      this.$store.commit('submitButtonLoadingStatus', true);
      let url = '';
      if (this.actionType === 'add') url = 'user/add';
      else url = 'user/update'
      this.axiosPost(url, {
        staffId: this.staffId,
        staffName: this.staffName,
        email: this.email,
        mobile: this.mobile,
        status: this.status,
        userType: this.userType,
        password: this.password,
        selectedSubMenu: this.allSubMenuId,
      }, (response) => {
        this.successNoti(response.message);
        $("#add-edit-dept").modal("toggle");
        bus.$emit('refresh-datatable');
        this.$store.commit('submitButtonLoadingStatus', false);
      }, (error) => {
        this.errorNoti(error);
        this.$store.commit('submitButtonLoadingStatus', false);
      })
    },

  }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
