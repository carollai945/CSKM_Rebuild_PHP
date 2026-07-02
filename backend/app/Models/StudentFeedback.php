<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class StudentFeedback extends Model {
    use HasFactory;
    protected $fillable = ['student_id','category','content','status','handled_by','reply'];
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function handler(): BelongsTo { return $this->belongsTo(Staff::class,'handled_by'); }
}
