<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RatioSnapshot extends Model
{
    protected $fillable = [
        'report_period_id', 'project_site_id', 'ratio_code', 'value', 'computed_at',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:4',
            'computed_at' => 'datetime',
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
}
