<?php

namespace App\Models;

use App\Models\Concerns\ScopedToSites;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Journal extends Model
{
    use ScopedToSites;

    protected $fillable = [
        'report_period_id',
        'project_site_id',
        'source',
        'status',
        'reference_no',
        'description',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
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

    public function lines(): HasMany
    {
        return $this->hasMany(JournalLine::class)->orderBy('line_order');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isBalanced(): bool
    {
        $debit = $this->lines->sum('debit');
        $credit = $this->lines->sum('credit');

        return round((float) $debit, 2) === round((float) $credit, 2);
    }
}
