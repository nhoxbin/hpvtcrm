<?php

use App\Enums\SalesStateEnum;
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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 12);
            $table->string('data', 100)->nullable();
            $table->dateTime('registered_at')->nullable();
            $table->dateTime('expired_at')->nullable();
            $table->text('available_data')->nullable();
            $table->string('sales_note')->nullable();
            $table->string('admin_note')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->enum('sales_state', SalesStateEnum::names())->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
