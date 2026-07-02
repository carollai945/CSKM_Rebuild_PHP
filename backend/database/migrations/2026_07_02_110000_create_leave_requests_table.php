<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->string('leave_type', 30); // ANNUAL / SICK / PERSONAL / OTHER
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->text('reason')->nullable();
            $table->string('status', 20)->default('PENDING'); // PENDING / APPROVED / REJECTED / CANCELLED
            $table->foreignId('approved_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->text('reject_reason')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('leave_requests'); }
};
