<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Announcement extends Model {
    use HasFactory;
    protected $fillable = ['staff_id','title','content','target_scope','status','publish_at'];
    protected function casts(): array { return ['publish_at'=>'datetime']; }
    public function staff(): BelongsTo { return $this->belongsTo(Staff::class); }
}
