<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FindByPriceRequest extends FormRequest
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
            'price_from' => ['required', 'numeric', 'lte:price_to'],
            'price_to' => ['required', 'numeric', 'gte:price_from'],
        ];
    }
}
