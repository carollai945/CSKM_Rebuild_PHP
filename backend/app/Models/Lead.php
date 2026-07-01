<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    protected $fillable = [
        'name',
        'gender',
        'phone',
        'mobile',
        'email',
        'education_level',
        'education_other',
        'source_code',
        'region_id',
        'assigned_staff_id',
        'status',
        'created_by',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'assigned_staff_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignmentHistory(): HasMany
    {
        return $this->hasMany(LeadAssignmentHistory::class);
    }
}
