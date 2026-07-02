<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model {
    use HasFactory;
    protected $fillable = ['staff_id','report_type','report_date','content','status'];
    protected function casts(): array { return ['report_date' => 'date:Y-m-d']; }
    public function staff(): BelongsTo { return $this->belongsTo(Staff::class); }
}
