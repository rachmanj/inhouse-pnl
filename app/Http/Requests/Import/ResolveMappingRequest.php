<?php

namespace App\Http\Requests\Import;

use Illuminate\Foundation\Http\FormRequest;

class ResolveMappingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'staging_row_id' => ['required', 'exists:sap_staging,id'],
            'account_id' => ['required', 'exists:accounts,id'],
        ];
    }
}
