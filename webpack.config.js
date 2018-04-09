const GoogleFontsPlugin = require("google-fonts-webpack-plugin");
var Encore = require('@symfony/webpack-encore');
const BabelMinifyPlugin = require("babel-minify-webpack-plugin");

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
;

if (Encore.isProduction()) {
    Encore
        .configureUglifyJsPlugin((options) => {
            options.test = /\.DISABLED$/i;
            return options;
        })
        .addPlugin(new BabelMinifyPlugin({}, {}))
}

module.exports = Encore.getWebpackConfig();
