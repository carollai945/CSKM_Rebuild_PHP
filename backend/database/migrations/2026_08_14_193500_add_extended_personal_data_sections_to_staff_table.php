<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->json('registered_address')->nullable()->after('photo_url');
            $table->json('mailing_address')->nullable()->after('registered_address');
            $table->json('language_abilities')->nullable()->after('mailing_address');
            $table->json('skills')->nullable()->after('language_abilities');
            $table->json('certifications')->nullable()->after('skills');
            $table->json('family_information')->nullable()->after('certifications');
            $table->json('work_experiences')->nullable()->after('family_information');
        });
    }

    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn([
                'registered_address',
                'mailing_address',
                'language_abilities',
                'skills',
                'certifications',
                'family_information',
                'work_experiences',
            ]);
        });
    }
};
