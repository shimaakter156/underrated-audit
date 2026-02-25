<template>
  <div class="container-fluid">
    <breadcrumb :options="['Ageing Report']"></breadcrumb>
    <!--STEP 1-->
    <div class="card">
      <div class="row">
        <div class="col-md-4">
          <button type="button" class="btn btn-primary btn-sm" @click="exportData">Export to Excel</button>
        </div>
      </div>
      <div class="table-condensed">
        <advanced-datatable :options="tableOptions" :advance="selectedAdvance" :business="business" :department="department">
          <template slot="outstanding" slot-scope="row">
            {{numberWithCommas(row.item.Outstanding)}}
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
            {{numberWithCommas(row.item['181-365'])}}
          </template>
          <template slot="six" slot-scope="row">
            {{numberWithCommas(row.item['366-Above'])}}
          </template>
        </advanced-datatable>
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
        source: 'finance/report/business-wise-advance',
        search: false,
        filterPayment: true,
        slots: [1,2,3,4,5,6,7],
        hideColumn: [],
        showFilter: ['','','','business',''],
        colSize: ['col-lg-2','col-lg-2','col-lg-1','col-lg-2','col-lg-2','col-lg-2','col-lg-2','col-lg-2'],
        slotsName: ['outstanding','one','two','three','four','five','six'],
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
      bus.$emit('export-data','Business-Wise-Advances-'+moment().format('YYYY-MM-DD'))
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