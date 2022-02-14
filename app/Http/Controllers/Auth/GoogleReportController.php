<?php

namespace ShowHeroes\Passport\Http\Controllers\Auth;

use Illuminate\Http\Response;
use JetBrains\PhpStorm\NoReturn;
use Laravel\Socialite\Two\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use ShowHeroes\Passport\Http\Controllers\Controller;
use ShowHeroes\Passport\Http\Gateways\Auth\GoogleGateway;
use ShowHeroes\Passport\Exceptions\Auth\LoginAuthorisation;

/**
 * Class GoogleReportController
 * @package ShowHeroes\Passport\Http\Controllers\Auth
 */
class GoogleReportController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return RedirectResponse|Response
     */
    public function redirect_to_google_provider(): Response|RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @param GoogleGateway $googleGateway
     * @param Guard $guard
     * @return RedirectResponse|Response
     */
    #[NoReturn] public function handle_google_provider_callback(
        GoogleGateway $googleGateway,
        Guard $guard
    ): Response|RedirectResponse
    {
        /** @var User $googleUser */
        $googleUser = Socialite::driver('google')->stateless()->user();

        try {
            $user = $googleGateway->getUserByEmail($googleUser);
        } catch (LoginAuthorisation $exception) {
            return redirect('/login')->withErrors($exception->getMessage());
        }

        Auth::login($user, true);

        return redirect()->intended('/dashboard/');
    }
}
