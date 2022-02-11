<?php

namespace ShowHeroes\Passport\Http\Auth\Gateways;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use ShowHeroes\Passport\Models\Teams\Membership;
use ShowHeroes\Passport\Models\User;
use ShowHeroes\Passport\Models\Teams\TeamSettings;
use ShowHeroes\Passport\Http\Requests\ApiRequest;
use ShowHeroes\Passport\Models\GoogleEntities\GoogleAuth;
use ShowHeroes\Passport\Exceptions\Auth\LoginAuthorisation;
use ShowHeroes\Passport\Notifications\Registration\WelcomeToATeamAfterGoogleAuth;

class GoogleGateway
{
    private array $exceptionEmails = [
        'webedia@showheroes.com',
        'do-not-reply@showheroes.com',
        'mtv@showheroes.com',
        'storage@showheroes.com',
    ];

    /**
     * Returns user if email domain can be automatically authorised.
     *
     * @param \Laravel\Socialite\Two\User $googleUser
     * @return User|null
     * @throws LoginAuthorisation
     */
    public function getUserByEmail(\Laravel\Socialite\Two\User $googleUser): ?User
    {
        $email = $googleUser->getEmail();

        if (in_array($email, $this->exceptionEmails, true)) {
            throw new LoginAuthorisation('Email is forbidden for automatic authorisation.');
        }

        /** @var User|null $user */
        $user = User::query()
            ->where('email', $email)
            ->first();

        if (!$user) {
            preg_match('|@(.*)$|iu', $email, $matches);
            if (!isset($matches[1]) || empty($matches[1])) {
                throw new LoginAuthorisation('Domain is not available for automatic log in.');
            }

            $mailDomain = $this->getOriginalFromAlias($matches[1]);

            /** @var TeamSettings|null $teamSettings */
            $teamSettings = TeamSettings::query()
                ->where('authorised_domain', $mailDomain)
                ->first();

            if (!$teamSettings) {
                throw new LoginAuthorisation('Domain doesn\'t match to any of existing teams.');
            }
            $team = $teamSettings->team();
        } else {
            $team = $user->currentTeam();
        }

        if (!$team) {
            throw new LoginAuthorisation('Related team not found.');
        }

        $isNewUser = false;
        $str = rand(6, 6);

        if (!$user) { // Create new user with specified Google data.
            /** @var User $user */
            $user = User::query()
                ->create(
                    [
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'password' => hash("sha256", $str)
                    ]
                );

            $isNewUser = true;
            $user->email_verified = true;
            $user->level = User::LEVEL_USER;
            $user->timezone = 'Europe/Berlin';

            // Resave photo URL.
            $googleOriginalAvatarURL = $googleUser->avatar_original ?? '';
            if ($googleOriginalAvatarURL) {
                $avatarContent = file_get_contents($googleOriginalAvatarURL);

                if (!empty($avatarContent)) {
                    $extension = 'jpg';
                    $urlParts = parse_url($googleOriginalAvatarURL);
                    $urlPath = $urlParts['path'] ?? '';
                    if ($urlPath) {
                        $fileExtension = pathinfo($urlPath, PATHINFO_EXTENSION);
                        if ($fileExtension) {
                            $extension = $fileExtension;
                        }
                    }
                    $disk = Storage::disk('users_images');
                    $avatarURL = '/users/avatars/u' . $user->id . '_' . hash("sha256", $str) . '.' . $extension;
                    $photoSaveSuccess = $disk->put($avatarURL, $avatarContent);
                    if ($photoSaveSuccess) {
                        $user->photo_url = $avatarURL;
                    }
                }
            }

            $user->save();
        }

        // Join user to a team if required.
        if (!$user->onTeam($team)) {
            Membership::query()
                ->create(
                    [
                        'user_id' => $user->id,
                        'team_id' => $team->id,
                        'role' => 'member',
                    ]
                );
        }

        // To update current user teams before switching
        $user = $user->fresh();

        if ($isNewUser) {
            $user->switchToTeam($team);
        }

        if ($isNewUser) {
         //   $user->notify(new WelcomeToATeamAfterGoogleAuth($team));
        }

        return $user;
    }

    /**
     * Returns a real domain name if alias is provided.
     * Short workaround for ShowHeroes.
     *
     * @param string $domainName
     * @return string
     */
    public function getOriginalFromAlias(string $domainName): string
    {
        if ($domainName === 'showheroes-group.com') {
            $domainName = 'showheroes.com';
        }
        return $domainName;
    }
}
