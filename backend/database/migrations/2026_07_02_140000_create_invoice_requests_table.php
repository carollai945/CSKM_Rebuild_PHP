<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('invoice_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->string('title', 200);
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->string('status', 20)->default('PENDING');
            $table->foreignId('approved_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->text('reject_reason')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('invoice_requests'); }
};
