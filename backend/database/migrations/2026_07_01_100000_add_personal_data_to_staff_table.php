<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('name');
            $table->string('gender', 10)->nullable()->after('phone');
            $table->string('blood_type', 5)->nullable()->after('gender');
            $table->date('birth_date')->nullable()->after('blood_type');
            $table->string('photo_url')->nullable()->after('birth_date');
        });
    }

    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn(['phone', 'gender', 'blood_type', 'birth_date', 'photo_url']);
        });
    }
};
