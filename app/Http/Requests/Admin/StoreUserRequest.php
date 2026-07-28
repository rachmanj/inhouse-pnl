<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => [$userId ? 'nullable' : 'required', 'string', 'min:8'],
            'dark_mode' => ['boolean'],
            'is_active' => ['boolean'],
            'role' => ['required', 'exists:roles,name'],
            'project_site_ids' => ['array'],
            'project_site_ids.*' => ['exists:project_sites,id'],
        ];
    }
}
