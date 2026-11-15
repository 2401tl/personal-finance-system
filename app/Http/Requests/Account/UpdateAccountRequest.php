<?php

namespace App\Http\Requests\Account;

use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:50'],
            'type' => ['sometimes', Rule::in([
                Account::TYPE_BANK,
                Account::TYPE_ALIPAY,
                Account::TYPE_WECHAT,
                Account::TYPE_CASH,
            ])],
            'balance' => ['sometimes', 'numeric'],
            'icon' => ['nullable', 'string', 'max:50'],
            'note' => ['nullable', 'string', 'max:255'],
        ];
    }
}
