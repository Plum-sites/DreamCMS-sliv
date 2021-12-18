const mix = require('laravel-mix')

mix
  .js('src/main.js', 'public/js')
  .webpackConfig({
    resolve: {
      alias: {
        '@': path.resolve(__dirname, 'src/'),
        '@themeConfig': path.resolve(__dirname, 'themeConfig.js'),
        '@core': path.resolve(__dirname, 'src/@core'),
        '@validations': path.resolve(__dirname, 'src/@core/utils/validations/validations.js'),
        '@axios': path.resolve(__dirname, 'src/libs/axios')
      }
    },
    module: {
      rules: [
        {
          test: /\.s[ac]ss$/i,
          use: [
            {
              loader: 'sass-loader',
              options: {
                sassOptions: {
                  includePaths: ['node_modules', 'src/assets']
                }
              }
            }
          ]
        },
        {
          test: /(\.(png|jpe?g|gif|webp)$|^((?!font).)*\.svg$)/,
          loaders: {
            loader: 'file-loader',
            options: {
              name: 'images/[path][name].[ext]',
              context: 'src/assets/images'
            }
          }
        }
      ]
    },
    output: {
      publicPath: '/assets/vuexy/',
      chunkFilename: 'chunks/[name].js',
    }
  })
  .sass('src/@core/scss/core.scss', 'public/css')
  .options({
    postCss: [require('autoprefixer'), require('postcss-rtl')]
  });

mix.copyDirectory('public', '../public/assets/vuexy');
mix.copyDirectory('chunks', '../public/assets/vuexy/chunks');
mix.copyDirectory('images', '../public/assets/vuexy/images');

mix.setResourceRoot("/assets/vuexy/");
