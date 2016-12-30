<?php

function array_transform($a)
{
	$o = [];
	foreach($a as $k => $v)
		$o[] = ['id' => $k, 'value' => $v];
	return $o;
}

function array_get_value_by_id($array, $id)
{
	foreach($array as $option)
		if($option['id'] == $id)
			return $option['value'];

	return null;
}

function array_add_blank($array)
{
	$new = [['id' => null, 'value' => '(None)']];

	return array_merge($new, $array);
}

function generate_sort_link($action, $id, $title, $def_col=null, $def_dir='ASC')
{
	$request = request();
	$params = $request->all();

	$arrow = "";

	if(!isset($params['sortCol']))
		$params['sortCol'] = $def_col ? $def_col : '';

	if(!isset($params['sortDir']))
		$params['sortDir'] = $def_dir;

	if($params['sortCol'] == $id)
	{
		if($params['sortDir'] == 'ASC')
		{
			$params['sortDir'] = 'DESC';
			$arrow = " &#9650;"; // UP
		}
		else
		{
			$params['sortDir'] = 'ASC';
			$arrow = " &#9660;"; // DOWN
		}
	}
	else
	{
		$params['sortDir'] = 'ASC';
	}

	$params['sortCol'] = $id;
	$params['page'] = 1;

	$url = action($action, $params);
	return "<a href='{$url}'>{$title}{$arrow}</a>";
}

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application(
    realpath(__DIR__.'/../')
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
