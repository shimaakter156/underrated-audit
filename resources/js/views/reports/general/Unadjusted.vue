<template>
  <div class="container-fluid">
    <breadcrumb :options="['Report of Unadjusted Advances']"></breadcrumb>
    <!--STEP 1-->
    <div class="card">
      <div class="row">
        <div class="col-md-4">
          <button type="button" class="btn btn-primary btn-sm" @click="exportData">Export to Excel</button>
        </div>
      </div>
      <div class="table-condensed">
        <advanced-datatable :options="tableOptions" :advance="selectedAdvance" :business="business" :department="department">
          <template slot="advance" slot-scope="row">
            {{numberWithCommas(row.item.AdvanceAmount)}}
          </template>
          <template slot="adjustment" slot-scope="row">
            {{numberWithCommas(row.item.AdjustmentAmount)}}
          </template>
          <template slot="outstanding" slot-scope="row">
            {{numberWithCommas(row.item.OutstandingAmount)}}
          </template>
        </advanced-datatable>
      </div>
    </div>
  </div>
</template>

<script>
import {Common} from "../../../mixins/common";
import {bus} from "../../../app";
import moment from "moment";

export default {
  mixins: [Common],
  data() {
    return {
      selectedAdvance: [],
      business: [],
      department: [],
      tableOptions: {
        source: 'adjustments/report/unadjusted',
        search: false,
        filterPayment: true,
        slots: [8,9,10],
        hideColumn: [],
        showFilter: ['requestId','advanceId','resStaffId','business','department'],
        colSize: ['col-lg-2','col-lg-2','col-lg-1','col-lg-2','col-lg-2','col-lg-2','col-lg-2','col-lg-2'],
        slotsName: ['advance','adjustment','outstanding'],
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
      bus.$emit('export-data','Unadjusted-Advance-Report-'+moment().format('YYYY-MM-DD'))
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