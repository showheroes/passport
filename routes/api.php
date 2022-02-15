<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use ShowHeroes\Passport\Http\Controllers\Auth\ApiAuthController;

//register new user
Route::post('/registration', [ApiAuthController::class, 'createAccount']);
//login user
Route::post('/login', [ApiAuthController::class, 'signin']);

//using middleware
Route::group(
    ['middleware' => ['auth:sanctum']], function () {
    Route::get(
        '/profile', function (Request $request) {
        return auth()->user();
    });

    Route::post('/sign-out', [Auth\ApiAuthController::class, 'logout'];
);


    Route::middleware('auth:sanctum')
        ->prefix('v1')
        ->group(
            function ($router) {
                /** @var \Illuminate\Routing\Router $router */
                $router->get('users/{id}', 'Api\Users\UsersApiController@show')->where('id', '[0-9]+');
                $router->get('users/current', 'Api\Users\UsersApiController@show_current');
            }
        );
