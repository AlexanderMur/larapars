import './bootstrap'; //always first
import 'select2';
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
    return axios.put(route('reviews.update', id), data);
}

function getEditPopup(href) {
    return $.get(href)
        .then(function (data) {
            const $myModal = $('#myModal');
            $myModal.find('.modal-body').html(data.data);

            $myModal.modal(data);
        });
}

function deleteReview(href) {
    return axios.delete(href);
}

function reloadDataTable() {
    if (window.LaravelDataTables) {
        window.LaravelDataTables.dataTableBuilder.ajax.reload();
    }
}

jQuery(function ($) {
    $(".nav-tabs a").click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });
    $(document)
        .on('click', '.model-edit', function (/**Event*/e) {
            getEditPopup(this.href);
            return false;
        })
        .on('click', '.model-trash', function (e) {
            deleteReview(this.href)
                .then(reloadDataTable)
                .catch(data => {
                    alert(data);
                });
            return false;
        })
        .on('submit', '.ajax-form', function () {
            $(this).find('.alert').remove();
            $(this).addClass('loading');
            $.ajax({
                method: $(this).find('input[name="_method"]').val(),
                url: this.action,
                data: $(this).serialize(),
            }).then((result) => {
                $(this).html(result);
                $(this).removeClass('loading');
                reloadDataTable();
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
    $('.js-data-example-ajax').select2({
        theme: "bootstrap",
        placeholder: 'Выберите компанию',
        ajax: {
            url: route('companies.search'),
            dataType: 'json',
            // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
            processResults: function (data) {
                const maped = data.data.map(company => ({id: company.id, text: company.title}));
                return {
                    results: maped,
                    pagination: {
                        more: data.next_page_url,
                    }
                };
            },
        },
    });
});
