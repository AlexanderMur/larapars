let mix = require('laravel-mix');


mix.setResourceRoot('../');
mix
    .js('resources/assets/admin.js', 'public/admin')
    // .sourceMaps('inline')
    .sass('resources/assets/admin.scss', 'public/admin')
    .browserSync({
        proxy: 'http://xdev.test/larapars/',
        files: [
            'public/**/*.css',
            'public/**/*.js',
        ],
        open: false,
        notification: false,
    })
    .disableNotifications()
mix.webpackConfig({
    output: {
        publicPath: '../public/',
        chunkFilename: 'admin/[name].js',
    },
});