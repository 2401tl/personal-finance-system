<?php

namespace App\Http\Requests\Category;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in([Category::TYPE_EXPENSE, Category::TYPE_INCOME])],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:50'],
            'icon' => ['required', 'string', 'max:50'],
            'color' => ['required', 'string', 'max:20'],
            'budget' => ['nullable', 'numeric', 'gte:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '请输入分类名称',
            'icon.required' => '请选择图标',
            'color.required' => '请选择颜色',
        ];
    }
}
