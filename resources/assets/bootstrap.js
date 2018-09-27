window.jQuery = window.$ = require('jquery');

window.axios = require('axios');

let token = document.head.querySelector('meta[name="csrf-token"]');

window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

$.ajaxSetup({
    beforeSend: function (xhr)
    {
        xhr.setRequestHeader("X-CSRF-TOKEN",token.content);
    }
});
