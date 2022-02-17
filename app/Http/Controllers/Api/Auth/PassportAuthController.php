<?php

namespace ShowHeroes\Passport\Http\Controllers\Api\Auth;

use Illuminate\Http\Response;
use JetBrains\PhpStorm\ArrayShape;
use ShowHeroes\Passport\Models\User;
use ShowHeroes\Passport\Models\Teams\Team;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use ShowHeroes\Passport\Http\Requests\Api\ApiRequest;
use ShowHeroes\Passport\Http\Requests\Api\Auth\SighInInUserRequest;
use ShowHeroes\Passport\Http\Requests\Api\Auth\RegistrationUserRequest;

/**
 * Class PassportAuthController
 * @package ShowHeroes\Passport\Http\Controllers\Api\Auth
 */
class PassportAuthController extends ApiRequest
{

    public const APP_FRONT_END_PREFIX = 'PassportFrontendAuthApp';

    public function register(RegistrationUserRequest $request): Response|Application|ResponseFactory
    {
        $data = [
            'name' => $request->getName(),
            'email' => $request->getEmail(),
        ];
        $data['password'] = bcrypt($request->getPass());
        $data['team_id'] = Team::getTeamByName($request->getTeam())->id;

        $user = User::create($data);

        $token = $user->createToken(self::APP_FRONT_END_PREFIX)->accessToken;

        return response(['user' => $user, 'token' => $token]);
    }

    //use this method to signin users
    public function signin(SighInInUserRequest $request): Application|ResponseFactory|Response
    {
        $data = [
            'email' => $request->getEmail(),
            'password' => $request->getPass()
        ];

        if (!auth()->attempt($data)) {
            return response(
                [
                    'error_message' => 'Incorrect Details. Please try again'
                ]
            );
        }

        $token = auth()->user()->createToken(self::APP_FRONT_END_PREFIX)->accessToken;

        return response(
            [
                'user' => auth()->user(),
                'token' => $token
            ]
        );
    }

    // this method signs out users by removing tokens
    #[ArrayShape(['message' => "string"])] public function signout(): array
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Tokens Revoked'
        ];
    }
}
