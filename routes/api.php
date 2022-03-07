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

Route::middleware('throttle:api')
    ->group(
        function ($router) {
            Route::post('/logout', 'Api\Auth\ApiUserAuthController@signout');
            $router->get('users/{id}', 'Api\Users\UsersApiController@show')->where('id', '[0-9]+');
            $router->get('users/current', 'Api\Users\UsersApiController@show_current');
        }
    );


// Ad Server user management API
Route::group(
    [
        'middleware' => ['auth:api', 'cors'],
        'prefix' => 'v1',
        'namespace' => 'Api'
    ],
    static function ($router) {
        /** @var Router $router */
        $router->get('users/current', '\ShowHeroes\Passport\Http\Controllers\Api\Users\UsersApiController@show_current');
    }
);
