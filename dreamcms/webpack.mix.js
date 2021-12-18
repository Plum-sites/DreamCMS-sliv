let mix = require('laravel-mix');
require('dotenv').config();

mix.options({
    processCssUrls: false
});

mix.webpackConfig({
    output: {
        chunkFilename: mix.inProduction() ? "assets/js/[name].[chunkhash].js" : "assets/js/[name].js"
    }
});

mix.sass('resources/css/style.scss', 'public/assets/css').version();
mix.js('resources/js/app.js', 'public/assets/js').version();

mix.copy('resources/js/unitpay.js', 'public/assets/js/unitpay.js')
   .copyDirectory('node_modules/tinymce/icons', 'public/assets/js/icons')
   .copyDirectory('resources/tinymce/plugins/spoiler', 'public/assets/js/plugins/spoiler')
    .copyDirectory('resources/font', 'public/assets/font')
    .copyDirectory('resources/img', 'public/assets/img')
    .copyDirectory('node_modules/tinymce/skins', 'public/assets/js/skins')
    .copyDirectory('node_modules/tinymce/plugins', 'public/assets/js/plugins')
    .copyDirectory('node_modules/@fortawesome/fontawesome-pro/sprites', 'public/assets/sprites')
    .copyDirectory('node_modules/@fortawesome/fontawesome-pro/webfonts', 'public/assets/webfonts')
    .copyDirectory('node_modules/@fortawesome/fontawesome-pro/svgs', 'public/assets/svgs')
;

mix.minify('public/assets/js/unitpay.js').version();