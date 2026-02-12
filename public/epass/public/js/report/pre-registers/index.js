/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */

"use strict";

$('.datepicker').datepicker({
    todayBtn : 'linked',
    format : 'dd-mm-yyyy',
    autoclose : true
});

function printDiv(divID) {
    var oldPage = document.body.innerHTML;
    var divElements = document.getElementById(divID).innerHTML;
    document.body.innerHTML = "<html><head><title></title></head><body>" + divElements + "</body>";

    window.print();
    document.body.innerHTML = oldPage;
    window.location.reload();
}

function html_table_to_excel(type)
 {
     var data = document.getElementById('attendance_data');
 
     var file = XLSX.utils.table_to_book(data, {sheet: "sheet1"});
 
     XLSX.write(file, { bookType: type, bookSST: true, type: 'base64' });
 
     XLSX.writeFile(file, 'attendance_report.' + type);
 }
 
 const export_button = document.getElementById('export_button');   
 
 export_button.addEventListener('click', () =>  {
     html_table_to_excel('xlsx');
 });


