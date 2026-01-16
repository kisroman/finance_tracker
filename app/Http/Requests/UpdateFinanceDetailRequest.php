<?php

namespace App\Http\Requests;

use App\Enums\CurrencyCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFinanceDetailRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('stock_id') && $this->input('stock_id') === '') {
            $this->merge(['stock_id' => null]);
        }
    }

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
            'stock_id' => ['sometimes', 'nullable', 'exists:stocks,id'],
            'source' => ['sometimes', 'string', 'max:120'],
            'amount' => ['sometimes', 'numeric'],
            'currency_code' => ['sometimes', Rule::in(CurrencyCode::values())],
            'is_active' => ['sometimes', 'boolean'],
            'comment' => ['sometimes', 'nullable', 'string', 'max:255'],
            'position' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
