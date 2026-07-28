<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PnlLine extends Model
{
    protected $fillable = [
        'code',
        'name',
        'parent_id',
        'sign',
        'is_subtotal',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_subtotal' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(PnlLine::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(PnlLine::class, 'parent_id');
    }

    public function coaMappings(): HasMany
    {
        return $this->hasMany(CoaMapping::class);
    }
}
