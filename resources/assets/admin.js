import {$, axios} from './bootstrap'

import 'select2'
import toastr from 'toastr'
//SB Admin 2
import 'sb-admin-2/vendor/bootstrap/js/bootstrap.js'
import 'sb-admin-2/vendor/datatables/js/dataTables.bootstrap.js'
import 'sb-admin-2/vendor/metisMenu/metisMenu.min.js'
import 'sb-admin-2/dist/js/sb-admin-2.js'
import 'mark.js/dist/jquery.mark.js'

import Vue from 'vue'

import Example from './components/Example.vue'
import Reviews from './components/Reviews.vue'
import route from 'ziggy' //нужно постоянно устанавливать

window.toastr = toastr

Vue.component(Example.name, Example)
Vue.component(Reviews.name, Reviews)

new Vue({
    el: '#wrapper',
})


function dataTable() {
    return window.LaravelDataTables.dataTableBuilder
}

function updateReview(id, data) {
    return axios.put(route('reviews.update', id), data)
}

function reviewsEndpoint(endpoint, id, data) {
    return $.get(route('reviews.' + endpoint, id), data)
}


function getEditPopup(href) {
    return $.get(href)
        .then(function (data) {
            const $myModal = $('#myModal')
            $myModal.find('.modal-body').html(data)

            $myModal.modal()
        })
}

function ajaxLoad(href, title = '') {
    return $.get(href)
        .then(function (data) {
            let modal = $('#myModal')
            modal
                .modal()
                .find('.modal-body')
                .html(data)

            modal.find('.modal-title').html(title)
        })
}

function deleteReview(href) {
    return axios.delete(href)
}

function reloadDataTable() {
    if (window.LaravelDataTables) {
        dataTable().ajax.reload()
    }
}

$(function ($) {
    $(document).on('click','.nav-tabs a',function (e) {
        e.preventDefault()
        $(this).tab('show')
    })
    $(document)
        .on('click', '.model-edit', function (e) {
            getEditPopup(e.target.href)
            return false
        })
        .on('click', '.model-trash', function (e) {
            deleteReview(e.target.href)
                .then(reloadDataTable)
                .catch(data => {
                    alert(data)
                })
            return false
        })
        .on('submit', '.ajax-form', function () {
            $(this).find('.alert').remove()
            $(this).addClass('loading')
            $.ajax({
                method: $(this).find('input[name="_method"]').val() || this.method,
                url: this.action,
                data: $(this).serialize(),
            }).then((result) => {
                $(this).html(result)
                $(this).removeClass('loading')
                reloadDataTable()
            })

            return false
        })
    $(document)
        .on('click', '.ajax-load', (e) => {
            ajaxLoad(e.target.href)
            return false
        })

    //like/dislike
    $(document)
        .on('click', '.like', function () {
            reviewsEndpoint('like', this.dataset.reviewId)
            $(this).parents('.review').remove()
        })
        .on('click', '.dislike', function () {
            reviewsEndpoint('dislike', this.dataset.reviewId)
            $(this).parents('.review').remove()
        })
        .on('click', '.like-review', function () {
            $(this).parents('._review').removeClass('review-bad').addClass('review-good')
            $.get(this.href)
            return false
        })
        .on('click', '.dislike-review', function () {
            $(this).parents('._review').removeClass('review-good').addClass('review-bad')
            $.get(this.href)
            return false
        })
    $('[data-toggle="tooltip"]').tooltip()
    $('.js-basic-multiple').select2();
    $('.company-select').select2({
        theme: 'bootstrap',
        placeholder: 'Выберите компанию',
        ajax: {
            url: route('companies.search'),
            dataType: 'json',
            // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
            processResults: function (data) {
                const maped = data.data.map(company => ({id: company.id, text: company.title}))
                return {
                    results: maped,
                    pagination: {
                        more: data.next_page_url,
                    },
                }
            },
        },
    })
    $(document)
        .on('click', '.reviews__nav-link', function (e) {
            let companyId = $(this).parents('.reviews__tabs').data('id')
            let scope = $(this).data('scope')
            $.get(route('parsed_companies.getReviews', companyId), {scope})
                .then(html => $(this).parents('.reviews__tabs')[0].outerHTML = html)
            return false
        })
        .on('click', '.reviews__tabs .page-link', function () {
            $.get(this.href)
                .then(html => $(this).parents('.reviews__tabs')[0].outerHTML = html)
            return false
        })
    $(document)
        .on('click', '.reviews__load-more', function () {
            $(this).button('loading')
            $.get(route('reviews.new'), {page: $(this).data('page') + 1})
                .then((data) => {
                    $(this).before(data.html)
                    $(this).data('page', data.currentPage)
                    $(this).button('reset')
                })
        })
    $('.final_data_choice_arrow')
        .on('click', '.data_choice--arrow__click', function (e) {
            const $parent = $(this).parents('.final_data_choice_arrow')
            $parent.find('.final_data').val($parent.find('.parsed_data').val())
            return false
        })

    $('.bulk-select').on('change', function () {
        const $form = $(this).parents('form')

        //remove classes starting with .selected-*
        $form.attr('class') && $form.attr('class').split(' ').forEach(function (classItem) {
            if (classItem.indexOf('selected-') === 0) {
                $form.removeClass(classItem)
            }
        })

        $form.addClass('selected-' + this.value)
    })

    if ($('#dataTableBuilder_length').length) {

        dataTable().on('search.dt', function () {
            dataTable().page.len(200)
        })
    }

    async function updateLogs() {

        let company_id = $('.parser__logs__inner').data('company_id')
        let json = {}
        if (company_id) {
            json = await $.get(route('parsers.logs'),{company_id})
        } else {
            json = await $.get(route('parsers.logs'))
        }
        if(json.id){
            $('.parser__start').parents('form').find('input[name="job_id"]').val(json.id)
        }
        $('#messages').html(json.messages)
        $('#http').html(json.http)




        $('.statistics').html(json.statistics)
        $('.parser__start').parents('form').attr('data-state', json.state)
        if (json.progress_max) {
            $('.parser__progress')
                .css({'width': (json.progress_now / json.progress_max) * 100 + '%'})
                .text(json.progress_now + ' из ' + json.progress_max)
        } else {
            // $('.parser__progress')
            //     .css({'width': 0 + '%'})
        }
        return json
    }


    async function startUpdateLogs() {

        if ($('.parser__logs__inner').length) {
            let json = await updateLogs()
            setTimeout(startUpdateLogs, 1000)
        }
    }
    startUpdateLogs()

    $('.parser__start').click(function () {
        $('.parser__logs__collapse').collapse('show')
        $.post(route('pars.test'), $(this).parents('form').serialize())
            .then((json) => {
                $(this).parents('form').attr('data-state', '')
                $(this).parents('form').find('input[name="job_id"]').val(json.id)
                updateLogs()
            })
            .catch((e) => {
                console.log(e)
                alert(JSON.stringify(e))
            })
        return false
    })
    $('.parser__resume').click(function () {
        $('.parser__logs__collapse').collapse('show')
        let job_id = $(this).parents('form').find('input[name="job_id"]').val();
        $.get(route('tasks.resume',job_id))
            .then((json) => {
                $(this).parents('form').attr('data-state', '')
                $(this).parents('form').find('input[name="job_id"]').val(json.id)
                updateLogs().then(() => {
                })
            })
            .catch((e) => {
                console.log(e)
                alert(JSON.stringify(e))
            })
        return false
    })
    $('.parser__stop').click(function () {
        $(this).button('loading').text('остановка...')
        let job_id = $(this).parents('form').find('input[name="job_id"]').val();
        $.get(route('tasks.pause',job_id))
            .then(() => {
                updateLogs().then(() => {
                    $(this).button('reset')
                })
            })
            .catch((e) => {
                console.log(e)
                alert(JSON.stringify(e))
            })
        return false
    })


    $(document).on('click', '.parsed-company__update', function () {
        $(this).button('loading')
        $.get(route('parsed_companies.getHistory', $(this).data('id')))
            .then(html => {
                $(this).parent().html(html)
                $(this).button('reset')
            })
        return false
    })

    let lastSearchTimeout = 0
    // language=CSS


    $(document).on('input', '.table__controls input', function (e) {
        clearTimeout(lastSearchTimeout)
        lastSearchTimeout = setTimeout(() => {
            $(this).parents('form').find('.dataTable').DataTable().ajax.reload()
        }, 300)
        return false
    })
})
