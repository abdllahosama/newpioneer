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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->foreignId('seller_id')->references('id')->on('users');
            $table->foreignId('buyer_id')->references('id')->on('users');
            $table->foreignId('investment_fund_id')->references('id')->on('investment_funds');
            $table->decimal('stock_price' , 10 ,2);
            $table->enum('approved' , [0,1])->default(0);
            $table->enum('status' , ['available' , 'soldout' , 'inreview' , 'cancelled'])->default('inreview');
            $table->date('sell_date');
            $table->date('approved_date');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
