<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('professors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('gender', 10)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('mobile', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('specialty')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('status', 20)->default('ACTIVE');
            $table->timestamps();
        });

        Schema::create('professor_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professor_id')->constrained('professors')->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professor_files');
        Schema::dropIfExists('professors');
    }
};
