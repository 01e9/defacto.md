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

    .autoProvidejQuery()

    .autoProvideVariables({
        'Popper': 'popper.js/dist/umd/popper'
    })

    .enableVersioning()

    .addPlugin(new GoogleFontsPlugin({
        fonts: [
            { family: "Merriweather", variants: [ "400", "700" ], subsets: [ "latin-ext" ] }
        ],
        name: 'google-fonts',
        path: 'google-fonts/',
        filename: 'google-fonts.css'
    }))

    .configureBabel(function(config) {
        config.presets = [
            "@babel/preset-env",
            "@babel/preset-es2015"
        ];
        config.plugins = [
            "@babel/plugin-proposal-class-properties",
            "@babel/plugin-proposal-object-rest-spread",
            "@babel/plugin-transform-modules-commonjs",
            "@babel/plugin-transform-destructuring",
            "@babel/plugin-syntax-dynamic-import",
            "@babel/plugin-syntax-export-default-from",
            "@babel/plugin-syntax-export-namespace-from"
        ];
    })
;

module.exports = Encore.getWebpackConfig();
