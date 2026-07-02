<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model {
    use HasFactory;
    protected $fillable = ['staff_id','leave_type','start_at','end_at','reason','status','approved_by','reject_reason'];
    protected function casts(): array { return ['start_at'=>'datetime','end_at'=>'datetime']; }
    public function staff(): BelongsTo { return $this->belongsTo(Staff::class); }
    public function approver(): BelongsTo { return $this->belongsTo(Staff::class,'approved_by'); }
}
