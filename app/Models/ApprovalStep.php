<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalStep extends Model
{
    protected $fillable = [
        'report_package_id', 'project_site_id', 'step_order', 'approver_role',
        'status', 'acted_by', 'acted_at', 'comments',
    ];

    protected function casts(): array
    {
        return ['acted_at' => 'datetime'];
    }

    public function reportPackage(): BelongsTo
    {
        return $this->belongsTo(ReportPackage::class);
    }

    public function projectSite(): BelongsTo
    {
        return $this->belongsTo(ProjectSite::class);
    }
}
