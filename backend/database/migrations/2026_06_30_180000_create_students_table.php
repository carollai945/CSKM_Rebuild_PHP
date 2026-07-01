<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_no', 50)->unique();
            $table->string('name');
            $table->string('gender', 10)->nullable();
            $table->foreignId('region_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->string('phone', 50)->nullable();
            $table->string('mobile', 50)->nullable();
            $table->string('fax', 50)->nullable();
            $table->string('address')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('email', 100)->nullable();
            $table->string('company_name')->nullable();
            $table->string('title_name', 100)->nullable();
            $table->string('source_code', 50)->nullable();
            $table->string('level_code', 50)->nullable();
            $table->foreignId('advisor_staff_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->string('status', 20)->default('ACTIVE');
            $table->timestamps();
        });

        Schema::create('student_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('status', 20)->default('ENROLLED');
            $table->date('joined_at')->nullable();
            $table->date('finished_at')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_courses');
        Schema::dropIfExists('students');
    }
};
