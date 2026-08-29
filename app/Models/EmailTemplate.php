<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'sender_pattern', 'subject_pattern', 'target', 'column_map',
    ];

    protected function casts(): array
    {
        return ['column_map' => 'array'];
    }
}
