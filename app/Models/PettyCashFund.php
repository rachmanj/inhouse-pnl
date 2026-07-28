<?php

namespace App\Models;

use App\Models\Concerns\ScopedToSites;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PettyCashFund extends Model
{
    use ScopedToSites;

    protected $fillable = [
        'project_site_id',
        'report_period_id',
        'opening_balance',
        'replenishment_amount',
        'closing_balance',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'opening_balance' => 'decimal:2',
            'replenishment_amount' => 'decimal:2',
            'closing_balance' => 'decimal:2',
        ];
    }

    public function projectSite(): BelongsTo
    {
        return $this->belongsTo(ProjectSite::class);
    }

    public function reportPeriod(): BelongsTo
    {
        return $this->belongsTo(ReportPeriod::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(PettyCashExpense::class);
    }
}
