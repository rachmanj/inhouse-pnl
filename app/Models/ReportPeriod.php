<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportPeriod extends Model
{
    protected $fillable = [
        'year',
        'month',
        'status',
        'baseline_year',
        'auto_deliver',
        'locked_at',
        'locked_by',
    ];

    protected function casts(): array
    {
        return [
            'locked_at' => 'datetime',
            'auto_deliver' => 'boolean',
        ];
    }

    public function lockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }
}
