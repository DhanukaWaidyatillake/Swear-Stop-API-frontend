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
        Schema::table('text_filter_audits', function (Blueprint $table) {
            $table->string('profanity_categories_caught')->after('response_body')->nullable();
            $table->string('profanity_caught')->after('response_body')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('text_filter_audits', function (Blueprint $table) {
            $table->dropColumn('profanity_categories_caught');
            $table->dropColumn('profanity_caught');
        });
    }
};
