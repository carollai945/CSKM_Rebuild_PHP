<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadAssignmentHistory extends Model
{
    protected $table = 'lead_assignment_history';

    protected $fillable = [
        'lead_id',
        'from_staff_id',
        'to_staff_id',
        'assigned_by',
        'assigned_at',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}
