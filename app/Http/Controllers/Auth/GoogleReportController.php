<?php

namespace ShowHeroes\Passport\Http\Controllers\Auth;

use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use ShowHeroes\Passport\Models\User;
use Laravel\Socialite\SocialiteManager;
use Laravel\Socialite\Two\GoogleProvider;
use ShowHeroes\Passport\Http\Gateways\Auth\GoogleGateway;
use ShowHeroes\Passport\Exceptions\Auth\LoginAuthorisation;

/**
 * Class GoogleReportController
 * @package ShowHeroes\Passport\Http\Controllers\Auth
 */
class GoogleReportController extends Controller
{

    /**
     * Redirect the user to the Google authentication page (for Youtube).
     *
     * @return Response
     */
    public function redirect_to_youtube_provider()
    {
        return Socialite::driver('google')
            ->with(['access_type' => 'offline', 'prompt' => 'consent'])
            ->scopes([
                \Google_Service_YouTube::YOUTUBE_UPLOAD,
                \Google_Service_YouTube::YOUTUBEPARTNER_CHANNEL_AUDIT,
                \Google_Service_YouTubeAnalytics::YT_ANALYTICS_READONLY
            ])
            ->redirect();
    }

    /**
     * Obtain the user information from Google (for Youtube).
     *
     * @param GoogleGateway $googleGateway
     * @param Guard $guard
     * @return Response
     */
    public function handle_youtube_provider_callback(GoogleGateway $googleGateway, Guard $guard)
    {
        /** @var \Laravel\Socialite\Two\User $googleUser */
        $googleUser = Socialite::driver('google')->user();
        /** @var User $localUser */
        $localUser = $guard->user();
        $googleGateway->authOrRememberUser($googleUser, $localUser);
        return redirect(route('web.page_to_close'));
    }

    /**
     * Redirect the user to the Google authentication page.
     *
     * @return Response
     */
    public function redirect_to_google_provider()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @param GoogleGateway $googleGateway
     * @param Guard $guard
     * @return Response
     */
    public function handle_google_provider_callback(GoogleGateway $googleGateway, Guard $guard)
    {
        /** @var \Laravel\Socialite\Two\User $googleUser */
        $googleUser = Socialite::driver('google')->user();
        try {
            $user = $googleGateway->getUserByEmail($googleUser);
        } catch (LoginAuthorisation $exception) {
            return redirect('/login')->withErrors($exception->getMessage());
        }

        Auth::login($user, true);
        return redirect()->intended('/home');
    }

    public function redirectToGoogleSheet()
    {
        /** @var SocialiteManager $socialiteManager */
        $socialiteManager = new SocialiteManager(app());
        /** @var GoogleProvider $provider */
        $provider = $socialiteManager->buildProvider(GoogleProvider::class, config('services.google_sheets'));
        return $provider
            ->with([ 'access_type' => 'offline' ])
            ->scopes([\Google_Service_Sheets::SPREADSHEETS_READONLY])
            ->redirect();
    }

    public function handleGoogleSheetCallback()
    {
        /** @var SocialiteManager $socialiteManager */
        $socialiteManager = new SocialiteManager(app());
        /** @var GoogleProvider $provider */
        $provider = $socialiteManager->buildProvider(GoogleProvider::class, config('services.google_sheets'));
        $user = $provider->user();
        dd($user);
    }
}
