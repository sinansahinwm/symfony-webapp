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

    // ---- Theme Modes
    .addEntry('app-theme-bordered-dark', './assets/app/theme/modes/theme-bordered-dark.js')
    .addEntry('app-theme-bordered-light', './assets/app/theme/modes/theme-bordered-light.js')

    .addEntry('app-theme-default-dark', './assets/app/theme/modes/theme-default-dark.js')
    .addEntry('app-theme-default-light', './assets/app/theme/modes/theme-default-light.js')

    .addEntry('app-theme-raspberry-dark', './assets/app/theme/modes/theme-raspberry-dark.js')
    .addEntry('app-theme-raspberry-light', './assets/app/theme/modes/theme-raspberry-light.js')

    .addEntry('app-theme-semidark-dark', './assets/app/theme/modes/theme-semidark-dark.js')
    .addEntry('app-theme-semidark-light', './assets/app/theme/modes/theme-semidark-light.js')

    // -- Stimulus Config
    .enableStimulusBridge('./assets/controllers.json')

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
