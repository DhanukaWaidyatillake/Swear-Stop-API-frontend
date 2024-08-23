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
            $table->boolean('is_free_request')->after('is_successful')->default(false)
                ->index('text_filter_audits_is_free_request_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('text_filter_audits', function (Blueprint $table) {
            $table->dropColumn('is_free_request');
        });
    }
};
