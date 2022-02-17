<?php

namespace ShowHeroes\Passport\Http\Requests\Api\Auth;

use JetBrains\PhpStorm\ArrayShape;
use ShowHeroes\Passport\Http\Requests\Api\ApiRequest;

/**
 * Class SighInInUserRequest
 * @package ShowHeroes\Passport\Http\Requests\Api\Auth
 */
class SighInInUserRequest extends ApiRequest
{
    #[ArrayShape(['email' => "string", 'password' => "string"])] public function rules(): array
    {
        return [
            'email' => 'required|string|email|',
            'password' => 'required|string|min:6'
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
            'shortcut.email' => 'Email :attribute is not valid.',
            'shortcut.min' => 'Password :attribute should has min 6 digits.',
        ];
    }

    public function getPass()
    {
        return $this->get('password');
    }

    public function getEmail()
    {
        return $this->get('email');
    }
}
