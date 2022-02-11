<?php

namespace ShowHeroes\Passport\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use ShowHeroes\Passport\Models\Teams\Team;
use ShowHeroes\Passport\Models\User;
use Laravel\Socialite\Facades\Socialite;
use ShowHeroes\Passport\Actions\Jetstream\AddTeamMember;

class OAuthController extends Controller
{
    /*
     * Login page
     */
    public function getLogin()
    {
        return view('login');
    }

    /*
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /*
     * Obtain the user information from the Google.
     */
    public function handleGoogleCallback()
    {
        $socialUser = Socialite::driver('google')->user();
        $user = User::where('email', '=', strtolower($socialUser->getEmail()))->first();
        if ($user) {
            $user->name = $socialUser->getName();
            $user->save();
            Auth::login($user);

        } else {
            $domains = $this->getDomainWithAliasesByEmail($socialUser->getEmail());
            /** @var Team $team */
            $team = Team::query()->whereIn('primary_domain', $domains)->first();
            if (!$team) {
                abort(403, 'A team for the user doesn\'t exist.');
            }

            $user = new User();
            $user->email = $socialUser->getEmail();
            $user->name = $socialUser->getName();
            $user->password = '';
            $user->current_team_id = $team->id;
            $user->save();

            // The first user of the team will become a team owner, others will be added like editors
            if (!$team->owner) {
                $team->user_id = $user->id;
                $team->save();
            } else {
                /** @var AddTeamMember $addTeamMember */
                $addTeamMember = app(AddTeamMember::class);
                $addTeamMember->add($team->owner, $team, $socialUser->getEmail(), 'editor');
            }
            Auth::login($user);
        }

        return redirect()->intended('/dashboard');
    }

    /*
     * Returns an array of the domain with aliases (if exist) from user email
     */
    protected function getDomainWithAliasesByEmail(string $email): array
    {
        $atPos = strpos($email, '@');
        if ($atPos === false) {
            return [];
        }
        $emailDomain = substr($email, $atPos + 1);
        $domainAliases = config('app.domains_aliases');
        foreach ($domainAliases as $mainDomain => $aliases) {
            if ($emailDomain == $mainDomain || in_array($emailDomain, $aliases)) {
                return array_merge([$mainDomain], $aliases);
            }
        }

        return [$emailDomain];
    }
}
