<?php

namespace App\Http\Requests\TitleRequest;

use Illuminate\Foundation\Http\FormRequest;

class TitleStoreRequest extends FormRequest
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
            "title"=>[
              'required',
              'min:5',
              'max:60',
            ],
            "content"=>[
              "required",
              "max:5000",
            ],
        ];
    }

    public function messages()
    {
        return [
            "title.required"=>"Başlık girilmesi zorunludur!",
            "title.min"=>"Başlığa minimum 5 karakter girebilirsiniz!",
            "title.max"=>"Başlığa maksimum 60 karakter girebilirsiniz!",
            "content.required"=>"Entry açıklaması girilmesi zorunludur!",
            "content.max"=>"Entry'e maksimum 60 karakter girilebilir!",
        ];
    }
}
