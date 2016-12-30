const elixir = require('laravel-elixir');

require('laravel-elixir-vue-2');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(mix => {
	mix.sass(['app.scss'], 'public/css/bootstrap.css');
	mix.webpack('app.js');

	mix.styles(['./node_modules/font-awesome/css/font-awesome.min.css',
	            './node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css']);

	mix.copy('node_modules/font-awesome/fonts', 'public/fonts');
	
	mix.version('js/app.js');
});
