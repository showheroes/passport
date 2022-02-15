<?php

namespace ShowHeroes\Passport\Http\Gateways\Auth;

use Illuminate\Support\Str;
use ShowHeroes\Passport\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use ShowHeroes\Passport\Models\Teams\Membership;
use ShowHeroes\Passport\Models\Teams\TeamSettings;
use ShowHeroes\Passport\Exceptions\Auth\LoginAuthorisation;

/**
 * Class GoogleGateway
 * @package ShowHeroes\Passport\Http\Gateways\Auth
 */
class GoogleGateway
{
    public const USER_PHOTO_PATH = '/users/avatars/user';
    public const TIME_ZONE = 'Europe/Berlin';

    public const MAIN_DOMAIN = 'showheroes.com';

    public const EXCEPTION_EMAIL = 'Email is forbidden for automatic authorisation.';
    public const EXCEPTION_DOMAIN = 'Domain is not available for automatic log in.';
    public const EXCEPTION_DOMAIN_TEAM = 'Domain doesn\'t match to any of existing teams.';
    public const EXCEPTION_RELATED_TEAM = 'Related team not found.';

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
            throw new LoginAuthorisation(self::EXCEPTION_EMAIL);
        }

        /** @var User|null $user */
        $user = User::query()
            ->where('email', $email)
            ->first();

        if (!$user) {
            preg_match('|@(.*)$|iu', $email, $matches);

            if (!isset($matches[1]) || empty($matches[1])) {
                throw new LoginAuthorisation(self::EXCEPTION_DOMAIN);
            }

            $mailDomain = $this->getOriginalFromAlias($matches[1]);

            /** @var TeamSettings|null $teamSettings */
            $teamSettings = TeamSettings::query()
                ->where('authorised_domain', $mailDomain)
                ->first();

            if (!$teamSettings) {
                throw new LoginAuthorisation(self::EXCEPTION_DOMAIN_TEAM);
            }

            $team = $teamSettings->team;
        } else {
            $team = $user->currentTeam();
        }

        if (!$team) {
            throw new LoginAuthorisation(self::EXCEPTION_RELATED_TEAM);
        }

        $isNewUser = false;

        $str = Hash::make(Str::random(8));

        if (!$user) { // Create new user with specified Google data.
            /** @var User $user */
            $user = User::query()
                ->create(
                    [
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'password' => $str
                    ]
                );

            $isNewUser = true;
            $user->email_verified = true;
            $user->level = User::LEVEL_USER;
            $user->timezone = self::TIME_ZONE;

            // Resave photo URL.
            $googleOriginalAvatarURL = $googleUser->getAvatar() ?? '';

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
                    $hashPath = Hash::make(Str::random(5));
                    $avatarURL = self::USER_PHOTO_PATH . $user->id . $hashPath . '.' . $extension;

                    $photoSaveSuccess = $disk->put($avatarURL, $avatarContent);

                    if ($photoSaveSuccess) {
                        $user->photo_url = $avatarURL;
                    }
                }
            }

            $user->save();
        }

        // To update current user teams before switching
        $user = $user->fresh();

        if ($isNewUser) {
            Membership::query()
                ->create(
                    [
                        'team_id' => $team->id,
                        'user_id' => $user->id,
                        'role' => 'member'
                    ]
                );

            $user->switchToTeam($team);
        }

        if ($isNewUser) {
            // probably need notification or sent an email
        }

        return $user;
    }

    /**
     * Returns a real domain name if alias is provided.
     * Short workaround for ShowHeroes.
     *
     * @param string $domainName
     * @return string|null
     */
    public function getOriginalFromAlias(string $domainName): ?string
    {
        $allowDomains = config('services.allow_domains');

        if ($allowDomains) {
            if (in_array($domainName, $allowDomains, true)) {
                return self::MAIN_DOMAIN;
            }
        }

        return null;
    }
}
