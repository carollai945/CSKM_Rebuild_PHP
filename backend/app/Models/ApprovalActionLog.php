<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ApprovalActionLog extends Model {
    public $timestamps = false;

    protected $fillable = ['related_type', 'related_id', 'actor_id', 'action', 'comment'];

    protected static function booted(): void {
        static::creating(function (self $model) {
            $model->created_at = $model->created_at ?? now();
        });
    }

    public function actor(): BelongsTo { return $this->belongsTo(User::class, 'actor_id'); }

    public function related(): MorphTo { return $this->morphTo(); }
}
