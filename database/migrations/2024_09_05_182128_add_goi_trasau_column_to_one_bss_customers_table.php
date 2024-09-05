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
            $table->text('goi_cuoc_ts')->nullable()->after('core_balance');
            $table->text('goi_cuoc')->nullable()->after('goi_cuoc_ts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('one_bss_customers', function (Blueprint $table) {
            $table->dropColumn(['goi_cuoc_ts', 'goi_cuoc']);
        });
    }
};
