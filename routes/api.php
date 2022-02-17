<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', 'Api\Auth\PassportAuthController@signin');
Route::post('/logout', 'Api\Auth\PassportAuthController@signout');

Route::middleware('auth:api')
    ->prefix('v1')
    ->group(
        function ($router) {
            Route::post('/register', 'Api\Auth\PassportAuthController@register');

            $router->get('users/{id}', 'Api\Users\UsersApiController@show')->where('id', '[0-9]+');
            $router->get('users/current', 'Api\Users\UsersApiController@show_current');
        }
    );
