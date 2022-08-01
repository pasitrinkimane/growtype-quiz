let mix = require('laravel-mix');

mix.setPublicPath('./public');
mix.setResourceRoot('./../')

mix
    .sass('resources/css/growtype-quiz-public.scss', 'css')
    .sass('resources/css/themes/rekviem.scss', 'css/themes')

mix
    .js('resources/js/growtype-quiz-public.js', 'js')

mix
    .copyDirectory('resources/icons', 'public/icons')

mix
    .sourceMaps()
    .version();


