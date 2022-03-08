<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['cors']],
    static function () {
        Route::post('/login', 'Api\Auth\ApiUserAuthController@signin');
        Route::post('/register', 'Api\Auth\ApiUserAuthController@register');
    }
);

Route::middleware(['auth:sanctum'])
    ->group(
        function ($router) {
            Route::post('/logout', 'Api\Auth\ApiUserAuthController@signout');
            $router->get('users/{id}', 'Api\Users\UsersApiController@show')->where('id', '[0-9]+');
        }
    );

Route::middleware(['middleware' => 'client_credentials', 'auth:api'])->group(
    function ($router) {
        $router->get('users/current', 'Api\Users\UsersApiController@show_current');
    });
