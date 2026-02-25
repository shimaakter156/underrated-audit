<template></template>
<script>
import XLSX from "xlsx";
import {bus} from "../../app";
import { saveExcel } from '@progress/kendo-vue-excel-export';

export default {
    data: () => ({
        data: [],
        headers: [],
        exportExcelData: [],
        columnHeader: [],
        excelFileName: "",
    }),
    mounted() {
        bus.$on("data-table-import",  (data, columns, fileName) => {
          this.exportExcelData = data;
          this.columnHeader = columns;
          this.excelFileName = fileName;
          this.excelExport();
        });
    },
    destroyed() {
        bus.$off('data-table-import');
    },
    methods: {
        excelExport () {
          console.log(this.columnHeader)
          console.log(this.exportExcelData)
          this.data = [];
          let headers = [];
          this.columnHeader.forEach((item) => {
            headers.push({
              field: item.key,
              title: item.title
            })
          })
          // this.exportExcelData.forEach((dataValue, dataIndex) => {
          //   let object = [];
          //   header.forEach((value, index) => {
          //     let title = value.title;
          //
          //     if (this.headers.indexOf(title) === -1) {
          //       this.headers.push(title);
          //     }
          //     object[title] = dataValue[value.key];
          //   });
          //
          //   this.data.push(Object.assign({}, object));
          // });
          console.log(this.data)
          saveExcel({
            data: this.exportExcelData,
            fileName: this.excelFileName,
            columns: headers
          });
        },
        dataExport() {
            this.data = [];
            let header = this.columnHeader;
            this.exportExcelData.forEach((dataValue, dataIndex) => {
                let object = [];
                header.forEach((value, index) => {
                    let title = value.title;

                    if (this.headers.indexOf(title) === -1) {
                        this.headers.push(title);
                    }
                    object[title] = dataValue[value.key];
                });

                this.data.push(Object.assign({}, object));
            });
            let name = this.excelFileName + ".xlsx";
            let posWS = XLSX.utils.json_to_sheet(this.data, {
              raw: true
            });

            //Excel width set
            let columnWidth = [];

            this.headers.forEach((value, index) => {
                const max = this.data.reduce((prev, current) => {
                    if (prev[value] === undefined || prev[value] === null)
                        prev[value] = "";
                    if (current[value] === undefined || current[value] === null)
                        current[value] = "";
                    return prev[value].length > current[value].length ? prev : current;
                });

                if (max[value] === undefined || max[value] === null) {
                    max[value] = "";
                }

                let maxLength =
                    max[value].toString().length > value.length
                        ? max[value].toString().length
                        : value.length;
                columnWidth.push(Object.assign({wch: maxLength + 2}));
            });

            posWS["!cols"] = columnWidth;
            let wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, posWS, "AMS");
            XLSX.writeFile(wb, name);
            this.$emit("resetExport", false);
        },
    },
};
</script>
