<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,'.$userId,
            'password' => 'sometimes|string|min:8|confirmed',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.string' => 'The name must be a valid string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already taken.',
            'email.max' => 'The email may not be greater than 255 characters.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}
