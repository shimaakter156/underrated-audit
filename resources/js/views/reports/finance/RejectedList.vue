<template>
  <div class="container-fluid">
    <breadcrumb :options="['Rejected Advances']"></breadcrumb>
    <!--STEP 1-->
    <div class="card">
      <div class="card-body">
        <div class="table-condensed">
          <advanced-datatable :options="tableOptions" :advance="selectedAdvance" :business="business" :department="department" :paymentModes="paymentModes" :banks="banks">
            <template slot="advance" slot-scope="row">
              {{numberWithCommas(row.item.Amount)}}
            </template>
          </advanced-datatable>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {Common} from "../../../mixins/common";
import {baseurl} from "../../../base_url";

export default {
  mixins: [Common],
  data() {
    return {
      selectedAdvance: [],
      selectedPaymentMode: '',
      selectedBankID: '',
      business: [],
      department: [],
      paymentModes: [],
      banks: [],
      tableOptions: {
        source: 'finance/advance/rejected-list',
        search: false,
        filterPayment: true,
        slots: [9],
        hideColumn: ['PaymentModeID','BankID'],
        showFilter: ['requestId','advanceId','reqStaffId','resStaffId','business','department','paymentMode','bank'],
        colSize: ['col-lg-1','col-lg-1','col-lg-1','col-lg-1','col-lg-2','col-lg-2','col-lg-2','col-lg-2'],
        slotsName: ['advance'],
        pages: [20,50, 100],
        addHeader: [],
      },
    }
  },
  created() {
    this.getData();
  },
  methods: {
    getData() {
      let instance = this;
      this.axiosGet('advance/support-data', function (response) {
        instance.business = response.business;
        instance.department = response.department;
        instance.paymentModes = response.paymentModes;
        instance.banks = response.banks;
      }, function (error) {

      });
    },
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
</style>