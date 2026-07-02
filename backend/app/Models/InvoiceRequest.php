<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class InvoiceRequest extends Model {
    use HasFactory;
    protected $fillable = ['staff_id','title','amount','description','status','approved_by','reject_reason'];
    protected function casts(): array { return ['amount'=>'decimal:2']; }
    public function staff(): BelongsTo { return $this->belongsTo(Staff::class); }
}
