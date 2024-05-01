<?php

use App\Enums\OneBssSalesStateEnum;
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
        Schema::create('one_bss_customers', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 12);
            $table->integer('core_balance');
            $table->text('goi_data');
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->enum('sales_state', OneBssSalesStateEnum::names())->nullable();
            $table->string('sales_note')->nullable();
            $table->string('admin_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('one_bss_customers');
    }
};
