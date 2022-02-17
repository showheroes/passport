<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(
    ['middleware' => ['guest']], function ($router) {
    Route::get('auth/google/login', 'Auth\GoogleReportController@redirect_to_google_provider')->middleware(['guest'])->name('web.auth.google.login');
    Route::get('auth/google/callback', 'Auth\GoogleReportController@handle_google_provider_callback')->middleware(['guest'])->name('web.auth.google.callback');

    /** @var \Illuminate\Routing\Router $router */
    $router->get(
        '/',
        function () {
        //    return redirect('login');
        }
    );
});
