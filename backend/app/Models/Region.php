<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        'region_code',
        'region_name',
        'region_english_name',
        'abbr',
        'status',
        'created_by',
        'updated_by',
    ];
}
