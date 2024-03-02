const Encore = require('@symfony/webpack-encore');

// -- Runtime Config
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // -- Output Config
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    // -- Entry Config
    .addEntry('app', './assets/app.js')
    .addEntry('app-theme-dark', './assets/app/theme/theme-dark.js')
    .addEntry('app-theme-light', './assets/app/theme/theme-light.js')

    // -- Stimulus Config
    .enableStimulusBridge('./assets/app/controllers.json')

    // -- Other Stuff
    .splitEntryChunks()
    .enableSingleRuntimeChunk()

    // -- Feature Config
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    // -- Babel Config
    // .configureBabel((config) => {
    //     config.plugins.push('@babel/a-babel-plugin');
    // })

    // enables and configure @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    })

    // Loaders
    .enableSassLoader()
    .enableTypeScriptLoader()
    //.enableReactPreset()

    // -- Integrity Hashes
    .enableIntegrityHashes(Encore.isProduction())

    // -- JQuery
    .autoProvidejQuery()

    // -- Copy Files
    .copyFiles({
        from: './assets/app/media',
        to: 'media/[path][name].[ext]',
    })

;

module.exports = Encore.getWebpackConfig();
