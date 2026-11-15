<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'old_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', Password::min(6), 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'old_password.required' => '请输入当前密码',
            'old_password.current_password' => '当前密码不正确',
            'password.min' => '新密码至少6位',
            'password.confirmed' => '两次密码不一致',
        ];
    }
}
