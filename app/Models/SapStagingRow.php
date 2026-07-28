<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SapStagingRow extends Model
{
    protected $table = 'sap_staging';

    protected $fillable = [
        'import_batch_id', 'row_number', 'raw_account_code', 'raw_account_name',
        'raw_debit', 'raw_credit', 'raw_balance', 'raw_payload',
        'mapped_account_id', 'mapping_status', 'error_message',
    ];

    protected function casts(): array
    {
        return [
            'raw_debit' => 'decimal:2',
            'raw_credit' => 'decimal:2',
            'raw_balance' => 'decimal:2',
            'raw_payload' => 'array',
        ];
    }

    public function importBatch(): BelongsTo
    {
        return $this->belongsTo(ImportBatch::class);
    }

    public function mappedAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'mapped_account_id');
    }
}
