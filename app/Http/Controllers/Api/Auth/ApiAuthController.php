<?php

namespace ShowHeroes\Passport\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use ShowHeroes\Passport\Models\User;
use ShowHeroes\Passport\Http\Controllers\Api\ApiController;
use ShowHeroes\Passport\Http\Requests\Api\Auth\RegistrationUserRequest;
use ShowHeroes\Passport\Http\Requests\Api\Auth\RestorePasswordRequest;

/**
 * Class ApiAuthController
 * @package ShowHeroes\Passport\Http\Controllers\Auth
 */
class ApiAuthController extends ApiController
{

    public function createAccount(RegistrationUserRequest $request)
    {
        $attr = $request->validate();

        $user = User::create(
            [
            'name' => $attr['name'],
            'password' => bcrypt($attr['password']),
            'email' => $attr['email']
        ]
        );

        return $this->success(
            [
            'token' => $user->createToken('tokens')->plainTextToken
        ]
        );
    }

    //use this method to signin users
    public function signin(RestorePasswordRequest $request)
    {
        $attr = $request->validate();

        if (!Auth::attempt($attr)) {
            return $this->error('Credentials not match', 401);
        }

        return $this->success(
            [
            'token' => auth()->user()->createToken('API Token')->plainTextToken
        ]
        );
    }

    // this method signs out users by removing tokens
    public function signout()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Tokens Revoked'
        ];
    }
}
