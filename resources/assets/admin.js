import './configfure-jquery'

import "sb-admin-2/vendor/bootstrap/js/bootstrap.js";
import "sb-admin-2/vendor/datatables/js/dataTables.bootstrap.js";
import "sb-admin-2/vendor/metisMenu/metisMenu.min.js";
import "sb-admin-2/dist/js/sb-admin-2.js";


console.log(111);
console.log(window.jQuery, 111);
jQuery(function ($) {
    $('td').on('click', function () {
        console.log(jQuery(this).parent('table').index());
    });
});