<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnomalyAlert extends Model
{
    protected $fillable = [
        'report_period_id', 'project_site_id', 'account_id',
        'metric', 'observed_value', 'expected_value', 'z_score',
        'explanation', 'status',
    ];

    protected function casts(): array
    {
        return [
            'observed_value' => 'decimal:4',
            'expected_value' => 'decimal:4',
            'z_score' => 'decimal:4',
        ];
    }

    public function reportPeriod(): BelongsTo
    {
        return $this->belongsTo(ReportPeriod::class);
    }

    public function projectSite(): BelongsTo
    {
        return $this->belongsTo(ProjectSite::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
