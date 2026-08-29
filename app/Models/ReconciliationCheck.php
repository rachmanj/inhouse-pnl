<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ReconciliationCheck extends Model
{
    protected $fillable = [
        'import_batch_id', 'checkable_type', 'checkable_id',
        'sap_control_total', 'system_total', 'discrepancy',
        'is_reconciled', 'discrepancy_detail',
    ];

    protected function casts(): array
    {
        return [
            'sap_control_total' => 'decimal:2',
            'system_total' => 'decimal:2',
            'discrepancy' => 'decimal:2',
            'is_reconciled' => 'boolean',
            'discrepancy_detail' => 'array',
        ];
    }

    public function importBatch(): BelongsTo
    {
        return $this->belongsTo(ImportBatch::class);
    }

    public function checkable(): MorphTo
    {
        return $this->morphTo();
    }
}
