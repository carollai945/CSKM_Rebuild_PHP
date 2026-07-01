<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = [
        'region_id',
        'department_no',
        'department_name',
        'status',
    ];

    public function titles(): HasMany
    {
        return $this->hasMany(Title::class);
    }
}
