<?php

namespace ShowHeroes\Passport\Http\Requests\Api\Auth;

use App\Http\Requests\Request;

/**
 * Class SighInInUserRequest
 * @package ShowHeroes\Passport\Http\Requests\Api\Auth
 */
class SighInInUserRequest extends Request
{
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|',
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
        ];
    }
}
