<?php

namespace ShowHeroes\Passport\Http\Requests\Api\Auth;

use JetBrains\PhpStorm\ArrayShape;
use ShowHeroes\Passport\Models\Teams\Team;
use ShowHeroes\Passport\Http\Requests\Api\ApiRequest;

/**
 * Class RegistrationUserRequest
 * @package ShowHeroes\Passport\Http\Requests\Api\Auth
 */
class RegistrationUserRequest extends ApiRequest
{
    /**
     * @return string[]
     */
    #[ArrayShape(
        [
            'team' => "string",
            'name' => "string",
            'email' => "string",
            'password' => "string"
        ]
    )] public function rules(): array
    {
        return [
            'team' => 'required|string|in:' .
                implode(',', array_keys(array_flip(Team::getAllTeamsName()))),
            'name' => 'required|min:4',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    #[ArrayShape(['shortcut.email' => "string", 'shortcut.min' => "string"])] public function messages(): array
    {
        return [
            'email.email' => 'Email :attribute is not valid.',
            'password.min' => 'Password :attribute should has min 6 digits.',
            'email.unique' => 'User :attribute already exists.',
        ];
    }

    public function getTeam()
    {
        return $this->get('team');
    }

    public function getName()
    {
        return $this->get('name');
    }

    public function getEmail()
    {
        return $this->get('email');
    }

    public function getPass()
    {
        return $this->get('password');
    }
}
