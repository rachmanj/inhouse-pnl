<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportColumnMap extends Model
{
    protected $fillable = ['source_signature', 'column_map', 'times_used'];

    protected function casts(): array
    {
        return [
            'column_map' => 'array',
        ];
    }
}
