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
            $table->string('goi')->nullable()->after('goi_data')->default(null);
            $table->date('expired_at')->nullable()->after('goi')->default(null);
            $table->text('integration')->after('expired_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('one_bss_customers', function (Blueprint $table) {
            $table->dropColumn(['expired_at', 'goi', 'integration']);
        });
    }
};
