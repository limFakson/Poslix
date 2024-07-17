const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .options({
       processCssUrls: false
   });

if (mix.inProduction()) {
    mix.version();
    mix.minify('public/js/app.js');
    mix.minify('public/css/app.css');
}
 