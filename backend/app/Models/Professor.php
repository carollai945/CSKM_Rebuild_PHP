<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Professor extends Model
{
    protected $fillable = [
        'name',
        'gender',
        'phone',
        'mobile',
        'email',
        'specialty',
        'photo_path',
        'status',
    ];

    public function files(): HasMany
    {
        return $this->hasMany(ProfessorFile::class);
    }
}
