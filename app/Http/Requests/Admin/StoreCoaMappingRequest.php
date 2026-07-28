<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCoaMappingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $mappingId = $this->route('coa_mapping')?->id;

        return [
            'account_id' => ['required', 'exists:accounts,id'],
            'pnl_line_id' => ['required', 'exists:pnl_lines,id'],
            'effective_from' => ['required', 'date'],
            'version' => ['integer', 'min:1'],
        ];
    }
}
