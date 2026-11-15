<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'min:4', 'max:50', 'unique:users,name'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'size:11', 'unique:users,phone', 'regex:/^1[3-9]\d{9}$/'],
            'password' => ['required', 'string', Password::min(6)],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => '请输入用户名',
            'username.min' => '用户名至少4个字符',
            'username.unique' => '用户名已被占用',
            'email.required' => '请输入邮箱',
            'email.email' => '请输入有效邮箱',
            'email.unique' => '邮箱已被注册',
            'phone.required' => '请输入手机号码',
            'phone.size' => '请输入有效的手机号码',
            'phone.unique' => '该手机号已被注册',
            'phone.regex' => '请输入有效的手机号码',
            'password.min' => '密码至少6位',
        ];
    }
}
