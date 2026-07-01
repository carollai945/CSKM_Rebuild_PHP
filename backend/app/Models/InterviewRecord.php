<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterviewRecord extends Model
{
    protected $fillable = [
        'lead_id',
        'staff_id',
        'interview_date',
        'result_code',
        'content',
        'next_contact_date',
    ];

    protected function casts(): array
    {
        return [
            'interview_date'    => 'date:Y-m-d',
            'next_contact_date' => 'date:Y-m-d',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
