<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VarianceFlag extends Model
{
    protected $fillable = [
        'report_period_id', 'project_site_id', 'pnl_line_id',
        'comparison_type', 'delta_absolute', 'delta_percent',
        'severity', 'is_acknowledged',
    ];

    protected function casts(): array
    {
        return [
            'delta_absolute' => 'decimal:2',
            'delta_percent' => 'decimal:2',
            'is_acknowledged' => 'boolean',
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

    public function pnlLine(): BelongsTo
    {
        return $this->belongsTo(PnlLine::class);
    }
}
