<?php

namespace ShowHeroes\Passport\Http\Requests\Api\Auth;

use App\Http\Requests\Request;

/**
 * Class RegistrationUserRequest
 * @package ShowHeroes\Passport\Http\Requests\Api\Auth
 */
class RegistrationUserRequest extends Request
{
    public function rules(): array
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
    public function messages()
    {
        return [
            'shortcut.email' => 'Email :attribute is not valid.',
            'shortcut.min' => 'Password :attribute should has min 6 digits.',
        ];
    }
}
