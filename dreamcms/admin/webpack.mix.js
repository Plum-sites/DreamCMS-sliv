let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.autoload({
    'jquery': ['$', 'window.jQuery', 'jQuery'],
});

mix.webpackConfig({
    resolve: {
        alias: {
        	'Api': path.resolve(__dirname, 'resources/js/api/'),
            'WebServices': path.resolve(__dirname, 'resources/js/webServices/'),
        	'Components': path.resolve(__dirname, 'resources/js/components/'),
        	'Constants': path.resolve(__dirname, 'resources/js/constants/'),
        	'Container': path.resolve(__dirname, 'resources/js/container/'),
        	'Views': path.resolve(__dirname, 'resources/js/views/'),
        	'Helpers': path.resolve(__dirname, 'resources/js/helpers/'),
        	'Themes': path.resolve(__dirname, 'resources/js/themes/'),
        	'Pages': path.resolve(__dirname, 'resources/js/pages/')
        }
    },
	output: {
		publicPath: '/assets/admin/',
		chunkFilename: 'chunks/[name].js'
	}
});

mix.js("resources/js/main.js", "public/js").sass("resources/js/assets/scss/_style.scss", "public/css/style.css");
   
mix.options({ extractVueStyles: true });

if (mix.inProduction()) {
  //mix.version(["public/css/style.css", "public/js/main.js"]);
} else {
  mix.sourceMaps();
}

mix.copyDirectory('public/css', '../public/assets/admin/css');
mix.copyDirectory('public/js', '../public/assets/admin/js');
mix.copyDirectory('chunks', '../public/assets/admin/chunks');