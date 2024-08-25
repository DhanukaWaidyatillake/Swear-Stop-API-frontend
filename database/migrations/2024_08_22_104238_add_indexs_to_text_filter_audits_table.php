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
            $table->index('user_id','text_filter_audits_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('text_filter_audits', function (Blueprint $table) {
            $table->dropIndex('text_filter_audits_user_id');
        });
    }
};