<?php

namespace App\Http\Requests\Journals;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreJournalRequest extends FormRequest
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
            'reference_no' => ['required', 'string', 'max:255', 'unique:journals,reference_no,'.$this->route('journal')?->id],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'in:draft,pending_approval'],
            'lines' => ['required', 'array', 'min:2'],
            'lines.*.account_id' => ['required', 'exists:accounts,id'],
            'lines.*.debit' => ['nullable', 'numeric', 'min:0'],
            'lines.*.credit' => ['nullable', 'numeric', 'min:0'],
            'lines.*.memo' => ['nullable', 'string', 'max:255'],
            'lines.*.line_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $lines = $this->input('lines', []);
            $debit = collect($lines)->sum(fn ($l) => (float) ($l['debit'] ?? 0));
            $credit = collect($lines)->sum(fn ($l) => (float) ($l['credit'] ?? 0));

            if (round($debit, 2) !== round($credit, 2)) {
                $validator->errors()->add('lines', 'Journal entries must be balanced (total debits must equal total credits).');
            }
        });
    }
}
