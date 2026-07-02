<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Petition extends Model {
    use HasFactory;
    protected $fillable = ['staff_id','title','content','status','approved_by','reject_reason'];
    public function staff(): BelongsTo { return $this->belongsTo(Staff::class); }
    public function approver(): BelongsTo { return $this->belongsTo(Staff::class,'approved_by'); }
}
