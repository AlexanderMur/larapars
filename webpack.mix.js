let mix = require('laravel-mix');


mix.setResourceRoot('../');
mix
    .js('resources/assets/admin.js', 'public/admin')
    .sass('resources/assets/admin.scss', 'public/admin')
    .browserSync({
        proxy: 'http://xdev.test/larapars/',
        files: [
            'public/*.css'
        ],
        open: false,
        notification: false,
    })
    .disableNotifications()