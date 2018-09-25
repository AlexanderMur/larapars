import './configfure-jquery';

import "sb-admin-2/vendor/bootstrap/js/bootstrap.js";
import "sb-admin-2/vendor/datatables/js/dataTables.bootstrap.js";
import "sb-admin-2/vendor/metisMenu/metisMenu.min.js";
import "sb-admin-2/dist/js/sb-admin-2.js";

import Vue from 'vue';

import Example from './components/Example.vue';
import VModal from 'vue-js-modal'

Vue.use(VModal)

Vue.component(Example.name, Example);

new Vue({
    el: '#wrapper',
});
jQuery(function ($) {
    $(".nav-tabs a").click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });
    $('[data-toggle="tooltip"]').tooltip();
});
