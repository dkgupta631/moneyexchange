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
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('from_currency')->nullable();
            $table->string('to_currency')->nullable();
            $table->string('normal_buy_rate')->nullable();
            $table->string('normal_sell_rate')->nullable();
            $table->string('standard_buy_rate')->nullable();
            $table->string('standard_sell_rate')->nullable();
            $table->string('rate_date')->nullable();
            $table->string('ordering')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
