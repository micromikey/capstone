const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .browserSync({
       proxy: 'http://127.0.0.1:8000',
       files: [
           'app/**/*.php',
           'resources/views/**/*.blade.php',
           'public/js/**/*.js',
           'public/css/**/*.css'
       ],
       open: false,
       notify: false
   });
