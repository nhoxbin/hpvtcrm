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
        Schema::table('one_bss_customers', function (Blueprint $table) {
            $table->foreignId('account_id')->nullable()->after('checked_by_user_id')->constrained('one_bss_accounts')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('one_bss_customers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('account_id');
        });
    }
};
