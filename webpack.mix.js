const mix = require('laravel-mix');

mix.webpackConfig({
    optimization: {
        providedExports: false,
        sideEffects: false,
        usedExports: false
    }
});

mix.js('./resources/js/script.js', 'assets/js');
mix.sass('./resources/css/styles.scss', 'assets/css');

mix.disableNotifications();
mix.sourceMaps(false, 'source-map');
