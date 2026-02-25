<template>
  <div class="container-fluid">
    <breadcrumb :options="['E-Statement']"></breadcrumb>
    <!--STEP 1-->
    <div class="card">
      <div class="card-body">
        <div class="filter">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="business">Business</label>
                <select v-model="business" class="form-control">
                  <option value="">Select</option>
                  <option :value="business.Business" v-for="(business,index) in businessList">{{business.BusinessName}}</option>
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="department">Department</label>
                <select v-model="department" class="form-control">
                  <option value="">Select</option>
                  <option :value="department.ResStaffDepartment" v-for="(department,index) in departmentList">{{department.ResStaffDepartment}}</option>
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="empCode">Staff ID</label>
                <input type="text" v-model="staffId" class="form-control" placeholder="Search By Staff ID">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="closeDate">Closing Date</label>
                <datepicker v-model="closingDate" :dayStr="dayStr" placeholder="YYYY-MM-DD" :firstDayOfWeek="0"/>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <button type="button" @click="getData" class="btn btn-primary btn-sm" style="width: 120px;text-transform: uppercase;font-weight: bold;"><i class="fa fa-search"></i> Filter</button>
                <button type="button" @click="reset" class="btn btn-warning btn-sm" style="width: 120px;text-transform: uppercase;font-weight: bold;"><i class="fa fa-sync"></i> Reset</button>
              </div>
            </div>
          </div>
        </div>
        <hr>
        <div class="print-button">
          <button type="button" class="btn btn-primary btn-sm" style="width: 120px;text-transform: uppercase;font-weight: bold;" @click="print"><i class="fa fa-print"></i> Print</button>
          <button type="button" class="btn btn-info btn-sm" style="width: 120px;text-transform: uppercase;font-weight: bold;" v-if="loading"><i class="fa fa-spinner fa-spin"></i> Sending</button>
          <button type="button" class="btn btn-info btn-sm" style="width: 120px;text-transform: uppercase;font-weight: bold;" v-else @click="send"><i class="fa fa-fighter-jet"></i> Send</button>
        </div>
        <div class="table">
          <table class="upper-table table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline"
                 style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <thead>
            <tr>
              <th>SL</th>
              <th>
                <span>Check/Uncheck</span>
                <br>
                <div class="all">
                  <input type="checkbox" id="all" @change="allCheck"> <label for="all">All</label>
                </div>
              </th>
              <th>Statement Details</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(group,index) in data" :key="index">
              <td>{{index + 1}}</td>
              <td><input type="checkbox" @change="addToBox($event,group)"></td>
              <td>
                <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm"
                       style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                  <thead>
                  <tr>
                    <th>Requester Staff ID</th>
                    <th>Requester Staff Name</th>
                    <th>Responsible Staff ID</th>
                    <th>Responsible Staff Name</th>
                    <th>Requisition ID</th>
                    <th>Advance ID</th>
                    <th>Requisition Date</th>
                    <th>Advance Amount</th>
                    <th>Adjustment Amount</th>
                    <th>Refund Amount</th>
                    <th>Outstanding Amount</th>
                    <th>Voucher Date</th>
                    <th>Age In Days</th>
                    <th>Days Over</th>
                    <th>Purpose Of Advance</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr v-for="(item,index2) in group" :key="index2">
                    <td>{{ item.RequesterStaffID }}</td>
                    <td>{{ item.RequesterStaffName }}</td>
                    <td>{{ item.ResStaffID }}</td>
                    <td>{{ item.ResStaffName }}</td>
                    <td>{{ item.RequisitionID }}</td>
                    <td>{{ item.AdvanceID }}</td>
                    <td>{{ item.RequisitionDate }}</td>
                    <td>{{ numberWithCommas(Number(item.AdvanceAmount)) }}</td>
                    <td>{{ numberWithCommas(Number(item.AdjustmentAmount)) }}</td>
                    <td>{{ numberWithCommas(Number(item.RefundAmount)) }}</td>
                    <td>{{ numberWithCommas(Number(item.OutstandingAmount)) }}</td>
                    <td>{{ item.VoucherDate }}</td>
                    <td>{{ item.AgeInDays }}</td>
                    <td>{{ item.DaysOver > item.AgeInDays ? item.AgeInDays : item.DaysOver }}</td>
                    <td>{{ item.PurposeOfAdvance }}</td>
                  </tr>
                  </tbody>
                </table>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {Common} from "../../../mixins/common";
import {baseurl} from "../../../base_url";
import moment from "moment"

export default {
  mixins: [Common],
  data() {
    return {
      data: [],
      checkedData: [],
      businessList: [],
      departmentList: [],
      business: '',
      department: '',
      closingDate: moment().format('yyyy-MM-DD'),
      staffId: '',
      dayStr: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
      loading: false
    }
  },
  created() {
    this.getData();
  },
  methods: {
    getData() {
      let instance = this;
      this.axiosPost('finance/report/e-statement', {
        business: this.business,
        department: this.department,
        staffId: this.staffId,
        closingDate: this.closingDate
      },function (response) {
        instance.data = response.data
        instance.businessList = response.businessList
        instance.departmentList = response.departmentList
      }, function (error) {

      });
    },
    addToBox(e,group) {
      var action = this.checkedData.find(function(item) {
        return item[0].ResStaffID === group[0].ResStaffID;
      })
      if (action !== undefined) {
        let instance = this;
        this.checkedData.forEach(function(item,index){
          if (item[0].ResStaffID === action[0].ResStaffID) {
            instance.checkedData.splice(index,1);
          }
        });
      } else {
        this.checkedData.push(group);
      }
      if (this.checkedData.length === this.data.length) {
        $("input[type='checkbox']").prop('checked',true)
      } else {
        $("#all").prop('checked',false)
      }
      if (this.checkedData.length === 0) {
        $("input[type='checkbox']").prop('checked',false)
      }
    },
    allCheck(e) {
      let instance = this
      if (e.target.checked) {
        this.checkedData = []
        this.data.forEach(function(item) {
          instance.checkedData.push(item)
        })
        $("input[type='checkbox']").prop('checked',true)
      } else {
        this.checkedData = []
        $("input[type='checkbox']").prop('checked',false)
      }
    },
    print() {
      if (this.checkedData.length > 0) {
        this.$store.commit('eStatement',this.checkedData)
        this.$store.commit('closingDate',this.closingDate)
        this.$router.push({name: 'EStatementFinancePrint'})
      }
    },
    send() {
      if (this.checkedData.length > 0) {
        let instance = this;
        this.loading = true
        this.axiosPost('finance/report/e-statement/send', {
          checkedData: this.checkedData,
          closingDate: this.closingDate
        },function (response) {
          instance.loading = false
          instance.successNoti('Mail and SMS has been sent successfully');
          instance.checkedData = []
          $("input[type='checkbox']").prop('checked',false)
        }, function (error) {

        });
      }
    },
    reset() {
      this.business = ''
      this.department = ''
      this.staffId = ''
      this.getData()
    }
  }
}
</script>

<style scoped>
th {
  font-size: 10px;
}

input, textarea, select {
  font-size: 10px;
  padding: 3px;
}

.table > tbody > tr > td {
  padding: 0 !important;
}
</style>

<style>
.datepicker .picker .picker-content {
  width: 350px !important;
}
.all label {
  margin: 0 !important;
}
.all {
  padding: 5px;
}
.upper-table>thead th {
  vertical-align: middle !important;
  text-transform: uppercase;
  font-size: 10px;
  font-weight: bold;
  letter-spacing: 0.1em;
}
.print-button {
  margin-bottom: 5px;
}
.print-button button {
  width: 100px;
}
.datepicker {
  padding: 0 !important;
}
</style>