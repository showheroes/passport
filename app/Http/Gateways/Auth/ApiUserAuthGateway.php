<?php

namespace ShowHeroes\Passport\Http\Gateways\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JetBrains\PhpStorm\ArrayShape;
use ShowHeroes\Passport\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use ShowHeroes\Passport\Models\Teams\Team;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use ShowHeroes\Passport\Exceptions\Auth\ApiAuthExceptions;
use ShowHeroes\Passport\Http\Requests\Api\Auth\SighInInUserRequest;
use ShowHeroes\Passport\Http\Requests\Api\Auth\RegistrationUserRequest;

/**
 * Class ApiUserAuthGateway
 * @package ShowHeroes\Passport\Http\Gateways\Auth
 */
class ApiUserAuthGateway
{
    public const APP_USER_DOES_NOT_EXIST = 'User does not exist';
    public const APP_USER_PASS_MISMATCH = 'Password mismatch';
    public const APP_USER_LOGOUT_MESS = 'You have been successfully logged out!';
    public const APP_USER_HAS_NOT_BEEN_REGISTERED = 'User has not been registered!';
    public const APP_USER_HAS_BEEN_REGISTERED = 'User has already been registered!';

    /**
     * @param RegistrationUserRequest $request
     * @return array|Response|Application|ResponseFactory|null
     * @throws ApiAuthExceptions
     */
    public function registerUser(RegistrationUserRequest $request): array|Response|Application|ResponseFactory|null
    {
        $user = $this->isRegistratedUser($request->getEmail());

        if ($user) {
            throw new ApiAuthExceptions(self::APP_USER_HAS_BEEN_REGISTERED);
        }

        $data = [
            'name' => $request->getName(),
            'email' => $request->getEmail(),
        ];

        $data['password'] = bcrypt($request->getPass());
        $data['team_id'] = Team::getTeamByName($request->getTeam())->id;

        $user = User::create($data);

        if ($user) {
            return response($this->authenticate($request->toArray(), $request, $user));
        }

        throw new ApiAuthExceptions(self::APP_USER_HAS_NOT_BEEN_REGISTERED);
    }

    /**
     * @param SighInInUserRequest $request
     * @return Application|ResponseFactory|Response
     * @throws ApiAuthExceptions
     */
    public function signinUser(SighInInUserRequest $request): Application|ResponseFactory|Response
    {
        $user = $this->isRegistratedUser($request->getEmail());

        if ($user) {
            if (Hash::check($request->getPass(), $user->password)) {
                return response($this->authenticate($request->toArray(), $request, $user));
            }

            throw new ApiAuthExceptions(self::APP_USER_PASS_MISMATCH);
        }

        throw new ApiAuthExceptions(self::APP_USER_DOES_NOT_EXIST);
    }

    /**
     * @param array $credentials
     * @param Request $request
     * @return string[]
     */
    #[ArrayShape(
        [
            'redirect' => "string"
        ]
    )] protected function authenticate(array $credentials, Request $request, User $user): array
    {
        if (!Auth::check()) {
            $data = [
                'email' => $credentials['email'],
                'password' => $credentials['password']
            ];

            if (Auth::attempt($data)) {
                $request->session()->regenerate();
            }
        }

        return ['redirect' => 'user/' . $user->id];
    }

    /**
     * @param string $email
     * @return User|null
     */
    protected function isRegistratedUser(string $email): ?User
    {
        return User::where('email', $email)
            ->first();
    }
}
