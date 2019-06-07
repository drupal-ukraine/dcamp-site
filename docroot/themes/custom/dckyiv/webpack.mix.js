/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your application. See https://github.com/JeffreyWay/laravel-mix.
 |
 */
const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Configuration
 |--------------------------------------------------------------------------
 */
mix
  .setPublicPath('assets')
  .disableNotifications()
  .options({
    processCssUrls: false
  });

mix.copy('node_modules/font-proxima-nova-scss/*', 'src/sass/fonts/');
mix.copy('node_modules/font-proxima-nova-scss/fonts/*', 'assets/css/fonts/');

/*
 |--------------------------------------------------------------------------
 | SASS
 |--------------------------------------------------------------------------
 */
mix.sass('src/sass/dckyiv.style.scss', 'css');

/*
 |--------------------------------------------------------------------------
 | JS
 |--------------------------------------------------------------------------
 */
mix.js('src/js/dckyiv.script.js', 'js');

