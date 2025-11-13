<?php

namespace App\Http\Requests\AuthRequest;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|max:32',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email alanı zorunludur.',
            'email.email' => 'Email alanı geçerli bir email adresi olmalıdır.',
            'email.exists' => 'Bu email adresi ile kayıtlı bir kullanıcı bulunamadı.',
            'password.required' => 'Şifre alanı zorunludur.',
            'password.min' => 'Şifre alanı en az 8 karakter olmalıdır.',
            'password.max' => 'Şifre alanı en fazla 32 karakter olmalıdır.',
        ];
    }
}
