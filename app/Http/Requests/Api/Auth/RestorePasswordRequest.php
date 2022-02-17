<?php

namespace ShowHeroes\Passport\Http\Requests\Api\Auth;

use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class RestorePasswordRequest
 * @package ShowHeroes\Passport\Http\Requests\Api\Auth
 */
class RestorePasswordRequest extends FormRequest
{
    /**
     * @return string[]
     */
    #[ArrayShape(['email' => "string"])] public function rules(): array
    {
        return [
            'email' => 'required|string|email',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    #[ArrayShape(['shortcut.email' => "string"])] public function messages(): array
    {
        return [
            'shortcut.email' => 'Email :attribute is not valid.',
        ];
    }

    public function getEmail()
    {
        return $this->get('email');
    }
}
