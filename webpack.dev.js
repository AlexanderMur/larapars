// development config
const merge = require('webpack-merge');
const webpack = require('webpack');
const commonConfig = require('./webpack.common.js');
const fs = require('fs');
const settings = require('./settings_local');
const address = require('ip').address();
const path = require('path');
const HMRMode = process.env.HOT;


fs.writeFile(settings.dist_path + '/hot', address, () => {
});


module.exports = {
    resolve: {
        extensions: ['.ts', '.tsx', '.js', '.jsx'],
    },
    entry: {
        main: [
            './index.js',
        ],
    },

    stats: {
        children: false,
        excludeAssets: (assetName) => !/\.(js|css|html)$/.test(assetName),
        modules: false,
    },
    performance: {
        hints: false,
    },
    context: resolve(__dirname, settings.src_path),
    module: {
        rules: [

            {
                test: /\.(jsx|js)$/,
                loader: 'babel-loader',
            },
            {
                test: /\.tsx?$/,
                use: ['babel-loader', 'awesome-typescript-loader'],
            },

            {
                test: /\.(sa|sc|c)ss$/,
                use: [
                    HMRMode ? 'style-loader' : MiniCssExtractPlugin.loader,
                    'css-loader',
                    {
                        loader: 'postcss-loader',
                        options: {

                            plugins: [
                                require('autoprefixer')
                            ]
                        }
                    },
                    'sass-loader',
                ],
            },

            {
                test: /\.less$/,
                use: [
                    HMRMode ? 'style-loader' : MiniCssExtractPlugin.loader,
                    'css-loader',
                    {
                        loader: 'postcss-loader',
                        options: {

                            plugins: [
                                require('autoprefixer')
                            ]
                        }
                    },
                    'less-loader',
                ],
            },

            {
                test: /\.vue$/,
                loader: 'vue-loader'
            },
            {

                test: /\.(jpe?g|png|gif)$/i,
                loader: 'cache-loader!file-loader',
                options: {
                    name: HMRMode ? '[path][name].[hash:8].[ext]' : '[path][name].[ext]',
                },
            },
            {
                test: [/\.eot$/, /\.ttf$/, /\.svg$/, /\.woff$/, /\.woff2$/,],
                loader: 'file-loader',
                options: {
                    name: HMRMode ? 'fonts/[name].[hash:8].[ext]' : '[path][name].[ext]',
                },
            },
        ],
    },
    mode: 'development',
    devServer: {
        headers: {"Access-Control-Allow-Origin": "*"},
        host: address,
        overlay: true,
        hot: true,
        stats: {
            children: false,
            excludeAssets: (assetName) => !/\.(js|css|html)$/.test(assetName),
            modules: false,
        }
    },
    output: {
        filename: '[name].js',
        path: path.resolve(__dirname, settings.dist_path),
        chunkFilename: '[name].bundle.js',
    },
    watchOptions: {
        ignored: /node_modules/
    },
    devtool: 'cheap-module-eval-source-map',
    plugins: [
        HMRMode ? new webpack.HotModuleReplacementPlugin() : () => {
        }, // enable HMR globally
        new webpack.NamedModulesPlugin(), // prints more readable module names in the browser console on HMR updates
    ],
};

if (!HMRMode) {
    const browserSync = require('browser-sync');

    browserSync({
        // proxy: 'http://pars.ru',
        notify: false,
        ghostMode: false,
        files: ['app/dist/*.css', 'app/src/*.html',],
        server: {
            baseDir: "app/dist",
        },
    });
}


