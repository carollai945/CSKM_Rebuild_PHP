<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentCourse extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'status',
        'joined_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'joined_at'   => 'date:Y-m-d',
            'finished_at' => 'date:Y-m-d',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
