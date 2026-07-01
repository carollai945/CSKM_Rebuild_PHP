<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentService extends Model
{
    use HasFactory;

    protected $table = 'student_services';

    protected $fillable = [
        'student_id', 'staff_id', 'service_type', 'content', 'status', 'service_date',
    ];

    protected $casts = [
        'service_date' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
