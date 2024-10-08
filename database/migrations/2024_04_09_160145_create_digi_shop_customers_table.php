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
        Schema::create('digi_shop_customers', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number');
            $table->string('tkc')->default(0);
            $table->string('first_product_name')->nullable();
            $table->text('packages')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digi_shop_customers');
    }
};
