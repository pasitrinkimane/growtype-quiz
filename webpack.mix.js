let mix = require('laravel-mix');

mix.setPublicPath('./public');
mix.setResourceRoot('./../')

mix
    .sass('resources/css/growtype-quiz-public.scss', 'css')
    .sass('resources/css/growtype-quiz-public-theme-1.scss', 'css')

mix
    .js('resources/js/growtype-quiz-public.js', 'js')

mix
    .copyDirectory('resources/images', 'public/images')

mix
    .sourceMaps()
    .version();


