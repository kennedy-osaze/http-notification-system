<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/', function () use ($router) {
    return response()->json(['message' => 'HTTP Notification System']);
});

$router->post('/subscribe/{topic}', [
    'as' => 'subscribe', 'uses' => 'SubscriptionController@store'
]);

$router->post('/publish/{topic}', [
    'as' => 'publish', 'uses' => 'PublishController@store'
]);
