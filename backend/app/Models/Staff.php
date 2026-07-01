<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';

    protected $fillable = [
        'user_id',
        'staff_no',
        'name',
        'abbr',
        'region_id',
        'department_id',
        'title_id',
        'join_date',
        'leave_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'join_date' => 'date:Y-m-d',
            'leave_date' => 'date:Y-m-d',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function title(): BelongsTo
    {
        return $this->belongsTo(Title::class);
    }
}
