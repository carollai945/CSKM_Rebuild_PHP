<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->string('report_type', 20)->default('DAILY'); // DAILY / WEEKLY
            $table->date('report_date');
            $table->text('content')->nullable();
            $table->string('status', 20)->default('DRAFT'); // DRAFT / SUBMITTED / APPROVED / REJECTED
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('reports'); }
};
