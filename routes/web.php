<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/** @var Laravel\Lumen\Routing\Router $router */

$router->get('/', static function () use ($router) {
    return $router->app->version();
});

$router->get('/tasks', 'TaskController@index');
$router->post('/tasks', 'TaskController@store');
$router->put('/tasks/{taskId}', 'TaskController@update');
$router->delete('/tasks/{taskId}', 'TaskController@destroy');
