<?php

namespace App\Http\Requests\AuthRequest;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8|same:password',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Ad alanı zorunludur.',
            'name.string' => 'Ad alanı bir string olmalıdır.',
            'name.max' => 'Ad alanı en fazla 255 karakter olmalıdır.',
            'email.required' => 'Email alanı zorunludur.',
            'email.email' => 'Email alanı geçerli bir email adresi olmalıdır.',
            'email.unique' => 'Bu email adresi ile kayıtlı bir kullanıcı bulunmaktadır.',
            'password.required' => 'Şifre alanı zorunludur.',
            'password.string' => 'Şifre alanı bir string olmalıdır.',
            'password.min' => 'Şifre alanı en az 8 karakter olmalıdır.',
            'password.confirmed' => 'Şifre alanı ve şifre tekrar alanı eşleşmiyor.',
            'password_confirmation.required' => 'Şifre tekrar alanı zorunludur.',
            'password_confirmation.string' => 'Şifre tekrar alanı bir string olmalıdır.',
            'password_confirmation.min' => 'Şifre tekrar alanı en az 8 karakter olmalıdır.',
            'password_confirmation.same' => 'Şifre alanı ve şifre tekrar alanı eşleşmiyor.',
        ];
    }
}
