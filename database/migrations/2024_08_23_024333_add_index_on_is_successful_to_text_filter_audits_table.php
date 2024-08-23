<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('text_filter_audits', function (Blueprint $table) {
            $table->index('is_successful', 'text_filter_audits_is_successful_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('text_filter_audits', function (Blueprint $table) {
            $table->dropIndex('text_filter_audits_is_successful_index');
        });
    }
};
