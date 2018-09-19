import 'sb-admin-2/vendor/jquery/jquery.js'
import 'sb-admin-2/vendor/bootstrap/js/bootstrap.js'
import 'sb-admin-2/vendor/metisMenu/metisMenu.min.js'
import 'sb-admin-2/dist/js/sb-admin-2.js'


window.jQuery = jQuery

jQuery('td').on('click',function(){
    console.log(jQuery(this).parent('table').index())
})