<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('titles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('region_id')->nullable();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->string('title_no');
            $table->string('title_name');
            $table->string('status')->default('active');
            $table->timestamps();

            $table->unique(['department_id', 'title_no']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('titles');
    }
};
