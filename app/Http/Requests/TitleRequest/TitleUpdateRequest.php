<?php

namespace App\Http\Requests\TitleRequest;

use Illuminate\Foundation\Http\FormRequest;

class TitleUpdateRequest extends FormRequest
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
            'is_locked'=>'required|in:0,1',
            'is_pinned'=>'required|in:0,1',
            'lock_reason' => 'nullable|string|max:2000',
            'pin_reason' => 'nullable|string|max:2000',
        ];
    }
}
