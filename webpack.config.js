const GoogleFontsPlugin = require("google-fonts-webpack-plugin");

var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())

    .addEntry('app/scripts', './assets/app/js/index.js')
    .addStyleEntry('app/styles', './assets/app/css/index.scss')

    .addEntry('admin/scripts', './assets/admin/js/index.js')
    .addStyleEntry('admin/styles', './assets/admin/css/index.scss')

    .enableSassLoader()

    .createSharedEntry('common', [
        'jquery',
        'bootstrap'
    ])

    .autoProvidejQuery()

    .autoProvideVariables({
        'Waves': 'node-waves/src/js/waves'
    })
;

module.exports = Encore.getWebpackConfig();

module.exports.plugins.push(
    new GoogleFontsPlugin({
        fonts: [
            { family: "Stardos Stencil", variants: [ "400", "700" ] }
        ],
        name: 'google-fonts',
        path: 'google-fonts/',
        filename: 'google-fonts.css'
    })
);