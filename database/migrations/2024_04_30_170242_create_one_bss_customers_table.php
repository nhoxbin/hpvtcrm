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
            $table->string('phone', 12)->unique();
            $table->string('tra_sau', 1)->nullable();
            $table->integer('core_balance')->default(0);
            $table->text('goi_data')->nullable();
            $table->string('has_data', 1)->default(0);
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
