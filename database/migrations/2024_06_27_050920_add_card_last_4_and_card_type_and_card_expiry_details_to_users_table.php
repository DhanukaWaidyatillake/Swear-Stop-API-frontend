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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('card_last_4')->nullable()->after('remember_token');
            $table->string('card_type')->nullable()->after('remember_token');
            $table->date('card_expiry_date')->nullable()->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('card_last_4');
            $table->dropColumn('card_type');
            $table->dropColumn('card_expiry_date');
        });
    }
};
