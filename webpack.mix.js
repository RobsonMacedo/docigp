const mix = require('laravel-mix')
const { CleanWebpackPlugin } = require('clean-webpack-plugin');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .version()

/*
 |--------------------------------------------------------------------------
 | Plugins
 |--------------------------------------------------------------------------
 */

const LiveReloadPlugin = require('webpack-livereload-plugin')

mix.webpackConfig({
    plugins: [new LiveReloadPlugin(), new CleanWebpackPlugin({cleanOnceBeforeBuildPatterns: ['js/*', '!static-files*']})],

    output: {
        chunkFilename: 'js/chunks/[chunkhash].js',
    },
})
