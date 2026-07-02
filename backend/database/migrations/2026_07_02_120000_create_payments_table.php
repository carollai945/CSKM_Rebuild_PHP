<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('fee_item_id')->nullable()->constrained('fee_items')->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('TWD');
            $table->string('payment_method', 30)->nullable();
            $table->date('payment_date')->nullable();
            $table->string('status', 20)->default('PENDING'); // PENDING / FINANCE_CONFIRMED / ACADEMIC_CONFIRMED / REJECTED
            $table->foreignId('finance_confirmed_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->foreignId('academic_confirmed_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('payments'); }
};
