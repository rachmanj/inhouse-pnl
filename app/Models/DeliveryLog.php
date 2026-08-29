<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryLog extends Model
{
    protected $fillable = [
        'report_package_id', 'channel', 'recipient', 'artifact_hash',
        'status', 'error_message', 'sent_at',
    ];

    protected function casts(): array
    {
        return ['sent_at' => 'datetime'];
    }

    public function reportPackage(): BelongsTo
    {
        return $this->belongsTo(ReportPackage::class);
    }
}
