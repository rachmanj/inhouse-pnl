<?php

namespace App\Http\Requests\Import;

use Illuminate\Foundation\Http\FormRequest;

class StoreImportBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'report_period_id' => ['required', 'exists:report_periods,id'],
            'project_site_id' => ['required', 'exists:project_sites,id'],
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ];
    }
}
