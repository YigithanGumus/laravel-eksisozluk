<?php

namespace App\Http\Requests\EntryRequest;

use Illuminate\Foundation\Http\FormRequest;

class EntryStoreRequest extends FormRequest
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
            "content"=>[
                "required",
                "min:8",
                "max:5000",
            ],
        ];
    }

    public function messages()
    {
        return [
          'content.required'=>"Açıklama girilmesi zorunludur!",
          'content.min'=>"Açıklama minimum 8 karakter girilebilir!",
          'content.max'=>"Açıklama maksimum 5000 karakter girilebilir!",
        ];
    }
}
