<?php

namespace App\Http\Requests\Account;

use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'type' => ['required', Rule::in([
                Account::TYPE_BANK,
                Account::TYPE_ALIPAY,
                Account::TYPE_WECHAT,
                Account::TYPE_CASH,
            ])],
            'balance' => ['required', 'numeric'],
            'icon' => ['nullable', 'string', 'max:50'],
            'note' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '请输入账户名称',
            'balance.required' => '请输入有效金额',
        ];
    }
}
