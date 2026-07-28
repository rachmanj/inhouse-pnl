<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PnlSnapshotLine extends Model
{
    protected $fillable = [
        'pnl_snapshot_id', 'pnl_line_id', 'year', 'month', 'amount',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function snapshot(): BelongsTo
    {
        return $this->belongsTo(PnlSnapshot::class, 'pnl_snapshot_id');
    }

    public function pnlLine(): BelongsTo
    {
        return $this->belongsTo(PnlLine::class);
    }
}
