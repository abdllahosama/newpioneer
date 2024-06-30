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
        Schema::create('investor_records', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('national_id');
            $table->string('phone')->nullable();
            $table->string('country');
            $table->string('email')->unique();
            $table->string('address')->nullable();
            $table->foreignId('investment_fund_id')->references('id')->on('investment_funds');
            $table->string('stock_quantity')->nullable();
            $table->date('date_of_ownership');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investor_records');
    }
};
