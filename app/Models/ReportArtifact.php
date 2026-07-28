<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportArtifact extends Model
{
    protected $fillable = ['report_package_id', 'type', 'file_path', 'file_hash', 'generated_at'];

    protected function casts(): array
    {
        return ['generated_at' => 'datetime'];
    }

    public function reportPackage(): BelongsTo
    {
        return $this->belongsTo(ReportPackage::class);
    }
}
