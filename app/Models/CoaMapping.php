<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoaMapping extends Model
{
    protected $fillable = [
        'account_id',
        'pnl_line_id',
        'effective_from',
        'version',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'effective_from' => 'date',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function pnlLine(): BelongsTo
    {
        return $this->belongsTo(PnlLine::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
