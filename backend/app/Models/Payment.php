<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model {
    use HasFactory;
    protected $fillable = ['student_id','fee_item_id','amount','currency','payment_method','payment_date','status','finance_confirmed_by','academic_confirmed_by','note'];
    protected function casts(): array { return ['payment_date'=>'date:Y-m-d','amount'=>'decimal:2']; }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function feeItem(): BelongsTo { return $this->belongsTo(FeeItem::class); }
}
