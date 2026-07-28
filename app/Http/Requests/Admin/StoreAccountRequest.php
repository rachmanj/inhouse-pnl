<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sap_code' => ['required', 'string', 'max:20', 'unique:accounts,sap_code,'.$this->route('account')?->id],
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:accounts,id'],
            'account_type' => ['required', 'in:revenue,backcharge,cost_of_sales,employee_expense,admin_expense,depreciation,other'],
            'normal_balance' => ['required', 'in:debit,credit'],
            'level' => ['integer', 'min:0'],
            'is_postable' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }
}
