<template>
  <div class="container-fluid">
    <router-link :to="{name: 'EStatementFinance'}" style="float:right;margin-top: 20px;">Back to E-Statement</router-link>
    <breadcrumb :options="['E-Statement Print']"></breadcrumb>
    <!--STEP 1-->
    <div class="card">
      <div class="card-body">
        <div id="printDiv" v-if="eStatement.length > 0">
          <div class="loop" v-for="(row,index) in eStatement" :key="index">
            <div class="header">
              <h6>Dear Sir/Madam, </h6>
            </div>
            <div class="title">
              <p>Please find below your e-statement of Advance Outstanding at: {{moment(closingDate).format('DD-MM-YYYY')}}</p>
            </div>
            <div>
              <table style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                <thead>
                <tr>
                  <th>Requisition ID</th>
                  <th>Advance ID</th>
                  <th>Requester Staff ID</th>
                  <th>Responsible Staff</th>
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
                <tr v-for="(item,index2) in row" :key="index2">
                  <td>{{ item.RequisitionID }}</td>
                  <td>{{ item.AdvanceID }}</td>
                  <td>{{ item.RequesterStaffID }}</td>
                  <td>{{ item.ResStaffName }}</td>
                  <td>{{ item.RequisitionDate }}</td>
                  <td>{{ numberWithCommas(Number(item.AdvanceAmount)) }}</td>
                  <td>{{ index2 === row.length - 1 ? '' : numberWithCommas(Number(item.AdjustmentAmount)) }}</td>
                  <td>{{ index2 === row.length - 1 ? '' : numberWithCommas(Number(item.RefundAmount)) }}</td>
                  <td>{{ numberWithCommas(Number(item.OutstandingAmount)) }}</td>
                  <td>{{ item.VoucherDate }}</td>
                  <td>{{ item.AgeInDays }}</td>
                  <td>{{ item.DaysOver }}</td>
                  <td>{{ item.PurposeOfAdvance }}</td>
                </tr>
                </tbody>
              </table>
            </div>
            <div class="conclusion">
              <p>If you have any disagreement, please contact with following responsible persons through email or phone call within 7 working Days.</p>
              <ul>
                <li>Mr. Anwarul Islam; Email: anwarul@aci-bd.com; Cell No: 01714163038</li>
                <li>Mr. Shazzadul Islam; Email: shazzadul@aci-bd.com; Cell No: 01713053236</li>
              </ul>
            </div>
            <div class="sincerely">
              <p style="font-weight: bold;">Sincerely,</p>
              <p>Finance Department</p>
              <p>ACI Centre</p>
            </div>
            <hr>
          </div>
        </div>
        <div v-else style="text-align: center">
          <h4>No Data</h4>
          <router-link :to="{name: 'EStatementFinance'}">Go Back to E-Statement</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {Common} from "../../../mixins/common";
import {baseurl} from "../../../base_url";
import moment from 'moment'

export default {
  mixins: [Common],
  data() {
    return {

    }
  },
  computed: {
    eStatement() {
      return this.$store.state.eStatement
    },
    closingDate() {
      return this.$store.state.closingDate
    }
  },
  mounted() {
    if(this.eStatement.length > 0) {
      // setTimeout(function(){
      //
      // },2000)
      this.print();
    }
  },
  methods: {
    print() {
      $("#printDiv").printThis({
        importCSS: true,
        importStyle: true,
        copyTagClasses: true,
        copyTagStyles: true,
      });
    }
  }
}
</script>

<style scoped>
th {
  font-size: 13px;
  font-weight: bold;
}
table > thead > tr > th {
  border: 1px solid #3f41433d;
}
table > tbody > tr > td {
  padding: 0 0 0 5px !important;
  border: 1px solid #3f41433d;
}
#printDiv {
  padding: 0 20px;
}
.loop {
  margin: 45px 0;
}
.title p {
  font-weight: bold;
  font-size: 13px;
}
.conclusion {
  margin-top: 15px;
}
.sincerely p {
  margin-bottom: 5px !important;
}
</style>

<style>
</style>