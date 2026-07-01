<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_no',
        'name',
        'gender',
        'region_id',
        'phone',
        'mobile',
        'fax',
        'address',
        'birth_date',
        'email',
        'company_name',
        'title_name',
        'source_code',
        'level_code',
        'advisor_staff_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date:Y-m-d',
        ];
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function advisor(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'advisor_staff_id');
    }

    public function studentCourses(): HasMany
    {
        return $this->hasMany(StudentCourse::class);
    }
}
