<template>
  <div class="container-fluid">
    <breadcrumb :options="['Ageing Report PO']"></breadcrumb>
    <!--STEP 1-->
    <div class="card">
      <div class="row">
        <div class="col-md-4">
          <button type="button" class="btn btn-success btn-sm" @click="exportData">Export to Excel</button>
        </div>
      </div>
      <div class="table-condensed">
        <general-datatable :options="tableOptions" :advance="selectedAdvance" :business="business" :department="department">
          <template slot="advance" slot-scope="row">
            {{numberWithCommas(row.item.AdvanceAmount)}}
          </template>
          <template slot="expense" slot-scope="row">
            {{numberWithCommas(row.item.AdjustmentAmount)}}
          </template>
          <template slot="refund" slot-scope="row">
            {{numberWithCommas(row.item.RefundAmount)}}
          </template>
          <template slot="adjustment" slot-scope="row">
            {{numberWithCommas(row.item.TotalAdjustment)}}
          </template>
          <template slot="outstanding" slot-scope="row">
            {{numberWithCommas(row.item.OutstandingAmount)}}
          </template>
          <template slot="days-over" slot-scope="row">
            <span v-if="Number(row.item.DaysOver) > Number(row.item.AgeInDays)">{{row.item.AgeInDays}}</span>
            <span v-else>{{row.item.DaysOver}}</span>
          </template>
          <template slot="one" slot-scope="row">
            {{numberWithCommas(row.item['1-30'])}}
          </template>
          <template slot="two" slot-scope="row">
            {{numberWithCommas(row.item['31-60'])}}
          </template>
          <template slot="three" slot-scope="row">
            {{numberWithCommas(row.item['61-90'])}}
          </template>
          <template slot="four" slot-scope="row">
            {{numberWithCommas(row.item['91-180'])}}
          </template>
          <template slot="five" slot-scope="row">
            {{numberWithCommas(row.item['180-Above'])}}
          </template>
          <template slot="total-amount" slot-scope="row">
            {{numberWithCommas(row.item.TotalAmount)}}
          </template>
        </general-datatable>
      </div>
    </div>
  </div>
</template>

<script>
import {Common} from "../../../mixins/common";
import {bus} from "../../../app";
import moment from "moment"

export default {
  mixins: [Common],
  data() {
    return {
      selectedAdvance: [],
      business: [],
      department: [],
      tableOptions: {
        source: 'finance/report/ageing-po',
        search: false,
        filterPayment: true,
        slots: [13,14,15,16,17,20,23,24,25,26,27,28],
        hideColumn: [],
        showFilter: ['requestId','advanceId','resStaffId','poNumber','supplierId','closingDate'],
        colSize: ['col-lg-2','col-lg-2','col-lg-1','col-lg-2','col-lg-2','col-lg-2'],
        slotsName: ['advance','expense','refund','adjustment','outstanding','days-over','one','two','three','four','five','total-amount'],
        pages: [40, 100],
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
      }, function (error) {

      });
    },
    exportData() {
      bus.$emit('export-data','Ageing-Report-'+moment().format('YYYY-MM-DD'))
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
button {
  margin: 20px 0 0 20px;
}
</style>