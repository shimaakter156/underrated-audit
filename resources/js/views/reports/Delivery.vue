<template>
  <div class="content">
    <div class="container-fluid">
      <breadcrumb :options="['Delivery Reports']">
        <button class="btn btn-primary"  @click="exportData" >Export</button>
      </breadcrumb>
      <advanced-datatable v-if="isLoading" :options="tableOptions"/>
    </div>
  </div>
</template>
<script>
import {Common} from "../../mixins/common";
import {bus} from "../../app";
export default {
  mixins: [Common],
  data() {
    return {
      isShow: false,
      isLoading: false,
      tableOptions: {},
    }
  },
  created() {
    this.getData();
  },
  methods: {
    exportData(){
      bus.$emit('export-data','delivery-reports')
    },
    getData() {
      this.axiosGet('delivery-report-supporting-data', (response) => {
        let status = [
          {text: "All", value: "all"},
          ...response.status
        ];
        this.tableOptions = {
          source: 'delivery-reports',
          search: true,
          sortable: [2],
          textRight: [4],
          pages: [20, 50, 100],
          dateFormat: [6],
          filters: [
            {
              title: "Select Status",
              type: "dropdown",
              key: "status",
              value: '',
              options: status
            },
            {
              title: "Order Date Range",
              type: "rangepicker",
              key: "orderCreateDate",
              value: ''
            }
          ],
        }
        this.isLoading = true;
      }, (error) => {
        this.errorNoti(error);
      })
    }
  }
}
</script>

