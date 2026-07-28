<?php

namespace App\Models;

use App\Models\Concerns\ScopedToSites;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaxFiling extends Model
{
    use ScopedToSites;

    protected $fillable = [
        'report_period_id',
        'project_site_id',
        'tax_type',
        'filing_number',
        'due_date',
        'filed_at',
        'status',
        'amount_reported',
        'source',
        'sarang_erp_ref_id',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'filed_at' => 'datetime',
            'amount_reported' => 'decimal:2',
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

    public function payments(): HasMany
    {
        return $this->hasMany(TaxPayment::class);
    }
}
