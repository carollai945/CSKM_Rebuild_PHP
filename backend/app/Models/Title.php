<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Title extends Model
{
    protected $fillable = [
        'region_id',
        'department_id',
        'title_no',
        'title_name',
        'status',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
