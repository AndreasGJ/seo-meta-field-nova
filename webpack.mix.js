let mix = require('laravel-mix')
const path = require("path");

mix.setPublicPath('dist')
    .js('resources/js/field.js', 'js').vue({version: 3})
    .sass('resources/sass/field.scss', 'css')
    .webpackConfig({
        externals: {
            vue: 'Vue',
        },
        output: {
            uniqueName: 'vendor/package',
        }
    })
    .alias({
        'laravel-nova': path.join(__dirname, './vendor/laravel/nova/resources/js/mixins/packages.js'),
    })
