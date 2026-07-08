<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('approval_action_logs', function (Blueprint $table) {
            $table->id();
            $table->string('related_type', 100);
            $table->unsignedBigInteger('related_id');
            $table->foreignId('actor_id')->constrained('users')->cascadeOnDelete();
            $table->string('action', 20); // SUBMIT / APPROVE / REJECT / CANCEL
            $table->text('comment')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['related_type', 'related_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('approval_action_logs'); }
};
