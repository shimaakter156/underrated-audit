<template>
  <div>
    <div class="modal fade" id="add-edit-dept" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <div class="modal-title modal-title-font" id="exampleModalLabel">{{ title }}</div>
          </div>
          <ValidationObserver v-slot="{ handleSubmit }">
            <form class="form-horizontal" id="form" @submit.prevent="handleSubmit(onSubmit)" autocomplete="off">
              <!-- Hidden username field for accessibility -->
              <input type="text" name="username" autocomplete="username" style="display:none">
              <div class="modal-body">
                <div class="row">
                  <div class="col-12 col-md-6">
                    <ValidationProvider name="Staff ID" mode="eager" rules="required" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="staffId">Staff ID <span class="error">*</span></label>
                        <input type="text" class="form-control" :class="{'error-border': errors[0]}"
                               v-model="staffId" placeholder="Staff ID" :disabled="actionType==='edit'" autocomplete="off">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-6">
                    <ValidationProvider name="Staff Name" mode="eager" rules="required" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="staffName">Staff Name <span class="error">*</span></label>
                        <input type="text" class="form-control" :class="{'error-border': errors[0]}" id="staffName"
                               v-model="staffName" name="staff-name" placeholder="Staff Name"
                               :disabled="actionType==='edit'" autocomplete="off">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-6">
                    <ValidationProvider name="email" mode="eager" rules="required|email" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="email">Email <span class="error">*</span></label>
                        <input type="text" class="form-control" :class="{'error-border': errors[0]}"
                               id="email" v-model="email" placeholder="Email" autocomplete="off">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-6">
                    <ValidationProvider name="mobile" mode="eager" rules="required|min:11|max:11" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="mobile">Mobile <span class="error">*</span></label>
                        <input type="text" class="form-control" :class="{'error-border': errors[0]}"
                               id="mobile" v-model="mobile" placeholder="Mobile" autocomplete="off">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-6">
                    <ValidationProvider name="User Type" mode="eager" rules="required" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="userType">User Type <span class="error">*</span></label>
                        <multiselect v-model="userType" :options="userTypes" :multiple="false" :close-on-select="true"
                                     :clear-on-select="false" :preserve-search="true" placeholder="Select User Type"
                                     label="UserTypeName" track-by="UserTypeID">
                        </multiselect>
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-6">
                    <ValidationProvider name="Location" mode="eager" rules="required" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="location">Location <span class="error">*</span></label>
                        <multiselect v-model="location" :options="locationList" :multiple="true" :close-on-select="true"
                                     :clear-on-select="false" :preserve-search="true" placeholder="Select Location"
                                     label="LocationName" track-by="LocationCode">
                        </multiselect>
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>

                  <div class="col-12 col-md-6" v-if="actionType === 'add'">
                    <ValidationProvider name="password" mode="eager" rules="required|min:6" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="password">Password <span class="error">*</span></label>
                        <input type="password" class="form-control" :class="{'error-border': errors[0]}" id="password"
                               v-model="password" name="password" placeholder="Password" autocomplete="new-password">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-6" v-if="actionType === 'add'">
                    <ValidationProvider name="confirm" mode="eager" rules="required|min:6|confirmed:password" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="confirm">Confirm Password <span class="error">*</span></label>
                        <input type="password" class="form-control" :class="{'error-border': errors[0]}" id="confirm"
                               v-model="confirm" name="confirm" placeholder="Confirm Password" autocomplete="new-password">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-6">
                    <ValidationProvider name="Status" mode="eager" rules="required" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="status">Status <span class="error">*</span></label>
                        <select class="form-control" id="status" v-model="status">
                          <option value="1">Active</option>
                          <option value="0">Inactive</option>
                        </select>
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12">
                    <p class="font-weight-bold">Submenu Permission</p>
                  </div>
                  <div class="col-12 col-md-6" v-for="(submenu, index) in allSubMenu" :key="index">
                    <div class="form-group">
                      <div class="form-check">
                        <p><u>{{ submenu.MenuName }}</u></p>
                        <div v-for="(sub, index2) in submenu.all_sub_menus" :key="index2">
                          <input class="form-check-input" type="checkbox" :value="sub.SubMenuID"
                                 v-model="allSubMenuId" :id="'allSubMenu' + index + '-' + index2">
                          <label class="form-check-label" :for="'allSubMenu' + index + '-' + index2">
                            {{ sub.SubMenuName }}
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
import { bus } from "../../app";
import { Common } from "../../mixins/common";

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
      status: '1',
      password: '',
      confirm: '',
      userType: '',
      location: [],
      actionType: '',
      buttonShow: false,
      userTypes: [],
      locationList: [],
      allSubMenu: [],
      allSubMenuId: [],
    }
  },
  mounted() {
    $('#add-edit-dept').on('hidden.bs.modal', () => {
      this.$emit('changeStatus');
    });

    bus.$on('add-edit-user', (row) => {
      this.resetForm();
      if (row) {
        this.axiosGet('user/get-user-info/' + row.UserID, (response) => {
          const user = response.data;
          this.title = 'Update User';
          this.buttonText = 'Update';
          this.staffName = user.Name;
          this.staffId = user.UserID;
          this.mobile = user.PhoneNo;
          this.email = user.Email;
          this.status = user.Status;
          this.userType = {
            UserTypeName: user.user_type.UserTypeName,
            UserTypeID: user.user_type.UserTypeID
          };
          this.buttonShow = true;
          this.actionType = 'edit';
          this.getData();
        }, (error) => {
          console.log('Error fetching user info:', error);
        });
      } else {
        this.title = 'Add User';
        this.buttonText = 'Add';
        this.buttonShow = true;
        this.actionType = 'add';
        this.getData();
        this.getLocation();
      }
      $("#add-edit-dept").modal("toggle");
    });
  },
  destroyed() {
    bus.$off('add-edit-user');
  },
  methods: {
    resetForm() {
      this.staffId = '';
      this.staffName = '';
      this.mobile = '';
      this.email = '';
      this.status = '1';
      this.password = '';
      this.confirm = '';
      this.userType = '';
      this.location = [];
      this.allSubMenu = [];
      this.allSubMenuId = [];
      this.buttonShow = false;
    },
    getData() {
      this.axiosGet('user/modal', (response) => {
        this.userTypes = response.userTypes || [];
        this.allSubMenu = response.allSubMenus || [];
      }, (error) => {
        console.log('Error fetching modal data:', error);
      });
    },
    getLocation() {
      this.axiosGet('get-location-list', (response) => {
        this.locationList = response.data || response || [];
      }, (error) => {
        console.log('Error fetching locations:', error);
      });
    },
    onSubmit() {
      this.$store.commit('submitButtonLoadingStatus', true);
      const url = this.actionType === 'add' ? 'user/add' : 'user/update';
      this.axiosPost(url, {
        staffId: this.staffId,
        staffName: this.staffName,
        email: this.email,
        mobile: this.mobile,
        status: this.status,
        userType: this.userType,
        location: this.location,
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
      });
    },
  }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>