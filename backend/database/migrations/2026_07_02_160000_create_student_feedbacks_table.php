<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('student_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('category', 50)->nullable();
            $table->text('content');
            $table->string('status', 20)->default('OPEN');
            $table->foreignId('handled_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->text('reply')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('student_feedbacks'); }
};
