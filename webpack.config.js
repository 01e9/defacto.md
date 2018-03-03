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

    .addPlugin(new GoogleFontsPlugin({
        fonts: [
            { family: "Stardos Stencil", variants: [ "400", "700" ] },
            { family: "Roboto", variants: [ "400", "700" ] },
        ],
        name: 'google-fonts',
        path: 'google-fonts/',
        filename: 'google-fonts.css'
    }))

    .configureBabel(function(babelConfig) {
        babelConfig.presets.push(
            "@babel/preset-es2015",
            "@babel/preset-env"
        );
        babelConfig.plugins.push(
            "@babel/plugin-proposal-object-rest-spread",
            "@babel/plugin-proposal-class-properties"
        );
    })
;

module.exports = Encore.getWebpackConfig();
