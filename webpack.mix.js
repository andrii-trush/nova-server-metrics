let mix = require('laravel-mix')
let path = require('path')

require('./nova.mix')

mix.setPublicPath('dist')
    .js('resources/js/card.js', 'js')
    .sass('resources/sass/card.scss', 'css')
    .vue({ version: 3 })
    .nova('llaski/nova-server-metrics');
