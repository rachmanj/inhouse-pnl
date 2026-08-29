<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportPackage extends Model
{
    protected $fillable = [
        'report_period_id', 'status', 'created_by',
    ];

    public function reportPeriod(): BelongsTo
    {
        return $this->belongsTo(ReportPeriod::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function artifacts(): HasMany
    {
        return $this->hasMany(ReportArtifact::class);
    }

    public function approvalSteps(): HasMany
    {
        return $this->hasMany(ApprovalStep::class)->orderBy('step_order');
    }

    public function deliveryLogs(): HasMany
    {
        return $this->hasMany(DeliveryLog::class);
    }
}
