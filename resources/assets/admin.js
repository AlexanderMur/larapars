import './bootstrap'; //

import "sb-admin-2/vendor/bootstrap/js/bootstrap.js";
import "sb-admin-2/vendor/datatables/js/dataTables.bootstrap.js";
import "sb-admin-2/vendor/metisMenu/metisMenu.min.js";
import "sb-admin-2/dist/js/sb-admin-2.js";


import Vue from 'vue';

import Example from './components/Example.vue';
import Reviews from './components/Reviews.vue';
import route from "ziggy";

Vue.component(Example.name, Example);
Vue.component(Reviews.name, Reviews);

new Vue({
    el: '#wrapper',
});

function updateReview(id, data) {
    axios.put(route('reviews.update', id), data);
}

function getReviewPopup(href) {
    axios.get(href)
        .then(function (data) {
            const $myModal = $('#myModal');
            $myModal.find('.modal-body').html(data.data);

            $myModal.modal(data);
        });
}

jQuery(function ($) {
    $(".nav-tabs a").click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });
    $(document)
        .on('click', '.edit-review', function (e) {
            getReviewPopup(e.target.href);
            return false;
        })
        .on('submit', '.ajax-form', function () {
            $(this).find('.alert').remove();
            $(this).addClass('loading');
            $.ajax({
                method: $(this).find('input[name="_method"]').val(),
                url: this.action,
                data: $(this).serialize()
            }).then((result) => {
                $(this).html(result);
                $(this).removeClass('loading');
                if (window.LaravelDataTables) {
                    window.LaravelDataTables.dataTableBuilder.ajax.reload();
                }
            });

            return false;
        })
        .on('click', '.like', function () {
            updateReview(this.dataset.reviewId, {good: true});
            $(this).parents('.review').remove();
        })
        .on('click', '.dislike', function () {
            updateReview(this.dataset.reviewId, {good: false});
            $(this).parents('.review').remove();
        });
    $('[data-toggle="tooltip"]').tooltip();
});
