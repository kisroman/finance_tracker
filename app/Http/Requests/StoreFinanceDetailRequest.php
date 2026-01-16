<?php

namespace App\Http\Requests;

use App\Enums\CurrencyCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFinanceDetailRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->missing('stock_id') || $this->input('stock_id') === '') {
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
            'stock_id' => ['nullable', 'exists:stocks,id'],
            'source' => ['required', 'string', 'max:120'],
            'amount' => ['required', 'numeric'],
            'currency_code' => ['required', Rule::in(CurrencyCode::values())],
            'is_active' => ['required', 'boolean'],
            'comment' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
