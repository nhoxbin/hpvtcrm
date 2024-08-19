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
        Schema::table('digi_shop_customers', function (Blueprint $table) {
            $table->text('integration')->after('first_product_name')->nullable()->default(null);
            $table->text('long_period')->after('integration')->nullable()->default(null);
            $table->boolean('is_request')->default(false)->after('packages');
            $table->foreignId('user_id')->after('is_request')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('digi_shop_customers', function (Blueprint $table) {
            $table->dropColumn('integration', 'long_period', 'is_request');
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
