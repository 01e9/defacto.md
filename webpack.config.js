const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableSingleRuntimeChunk()
    .enableSassLoader()
    .enableTypeScriptLoader()
    .autoProvidejQuery()
    .autoProvideVariables({
        'Popper': 'popper.js/dist/umd/popper'
    })
    .enableVersioning()

    .addEntry('app/scripts', './assets/app/js/index.ts')
    .addStyleEntry('app/styles', './assets/app/css/index.scss')

    .addEntry('admin/scripts', './assets/admin/js/index.ts')
    .addStyleEntry('admin/styles', './assets/admin/css/index.scss')
;

/*if (Encore.isProduction()) {
    Encore
        .configureUglifyJsPlugin((options) => {
            options.test = /\.DISABLED$/i;
            return options;
        })
        .addPlugin(new BabelMinifyPlugin({}, {}))
}*/

module.exports = Encore.getWebpackConfig();
