import {$, axios} from "./bootstrap";

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


function dataTable() {
    return window.LaravelDataTables.dataTableBuilder;
}

function updateReview(id, data) {
    return axios.put(route('reviews.update', id), data);
}

function reviewsEndpoint(endpoint, id, data) {
    return $.get(route('reviews.' + endpoint, id), data);
}

function getEditPopup(href) {
    return $.get(href)
        .then(function (data) {
            const $myModal = $('#myModal');
            $myModal.find('.modal-body').html(data);

            $myModal.modal();
        });
}

function deleteReview(href) {
    return axios.delete(href);
}

function reloadDataTable() {
    if (window.LaravelDataTables) {
        dataTable().ajax.reload();
    }
}

$(function ($) {
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
        });


    //like/dislike
    $(document)
        .on('click', '.like', function () {
            reviewsEndpoint('like', this.dataset.reviewId);
            $(this).parents('.review').remove();
        })
        .on('click', '.dislike', function () {
            reviewsEndpoint('dislike', this.dataset.reviewId);
            $(this).parents('.review').remove();
        })
        .on('click', '.like-review', function () {
            $(this).parents('._review').removeClass('review-bad').addClass('review-good');
            $.get(this.href);
            return false;
        })
        .on('click', '.dislike-review', function () {
            $(this).parents('._review').removeClass('review-good').addClass('review-bad');
            $.get(this.href);
            return false;
        });
    $('[data-toggle="tooltip"]').tooltip();
    $('.company-select').select2({
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
                    },
                };
            },
        },
    });
    $(document)
        .on('click', '.reviews__nav-link', function (e) {
            let companyId = $(this).parents('.reviews__tabs').data('id');
            let scope = $(this).data('scope');
            $.get(route('parsed_companies.getReviews', companyId), {scope})
                .then(html => $(this).parents('.reviews__tabs')[0].outerHTML = html);
            return false;
        })
        .on('click','.reviews__tabs .page-link',function(){
            $.get(this.href)
                .then(html => $(this).parents('.reviews__tabs')[0].outerHTML = html);
            return false;
        })
    $('.final_data_choice_arrow')
        .on('click', '.data_choice--arrow__click', function (e) {
            const $parent = $(this).parents('.final_data_choice_arrow');
            $parent.find('.final_data').val($parent.find('.parsed_data').val());
            return false;
        });

    $('.bulk-select').on('change', function () {
        const $form = $(this).parents('form');

        //remove classes starting with .selected-*
        $form.attr('class') && $form.attr('class').split(' ').forEach(function (classItem) {
            if (classItem.indexOf('selected-') === 0) {
                $form.removeClass(classItem);
            }
        });

        $form.addClass('selected-' + this.value);
    });

    if ($('#dataTableBuilder_length').length) {

        dataTable().on('search.dt', function () {
            dataTable().page.len(200);
        });
    }

    async function updateLogs() {
        const json = await $.get(route('parsers.logs'));
        console.log(json);
        $('.logs').html(json.table);
        $('.statistics').html(json.statistics);
    }

    let canUpdateLogs = false;

    async function startUpdateLogs() {
        if (canUpdateLogs) {
            await updateLogs();
        }
        setTimeout(startUpdateLogs, 1000);
    }

    if ($('.statistics').length) {
        updateLogs();
    }
    startUpdateLogs();
    $('.start-parsing').click(function () {
        $(this).button('loading');
        canUpdateLogs = true;
        $.post(route('pars.test'), $(this).parents('form').serialize())
            .catch(() => alert('Ошибка'))
            .then(() => {
                $(this).button('reset');
                canUpdateLogs = false;
                updateLogs();
            });
        return false;
    });

});
