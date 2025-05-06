<?php

declare(strict_types=1);

namespace App\Domains\Authorization\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email:rfc,dns', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6','confirmed'],
            'password_confirmation' => ['required']
        ];
    }
}
