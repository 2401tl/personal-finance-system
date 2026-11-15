<?php

namespace App\Http\Requests\Transaction;

use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['sometimes', Rule::in([Transaction::TYPE_EXPENSE, Transaction::TYPE_INCOME])],
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'account_id' => ['sometimes', 'integer', 'exists:accounts,id'],
            'amount' => ['sometimes', 'numeric', 'gt:0'],
            'date' => ['sometimes', 'date'],
            'note' => ['nullable', 'string', 'max:255'],
        ];
    }
}
