let mix = require('laravel-mix');

console.log(mix.autoload,'aaaaaaaaaaa')

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