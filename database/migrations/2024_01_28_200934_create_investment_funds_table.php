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
        Schema::create('investment_funds', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('commercial_record')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('sector_id')->constrained('sectors')->nullable();
            $table->foreignId('user_id')->constrained('users')->nullable();
            $table->string('shareholder_registry_manager')->nullable();
            $table->string('shareholder_registry_email')->nullable();
            $table->string('broker_shareholder_registry')->nullable();
            $table->string('broker_shareholder_registry_email')->nullable();
            $table->enum('type', ['investment_funds', 'company'])->nullable();
            $table->string('stock_price')->nullable();
            $table->string('last_stock_price')->nullable();
            $table->string('stock_quantity')->nullable();
            $table->string('market_value')->nullable();
            $table->string('nominal_value')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->foreignId('category_id')->constrained('investment_fund_categories')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->text('details')->nullable();
            $table->enum('status', ['open', 'closed'])->nullable();
            $table->decimal('value', 10, 2)->nullable();
            $table->date('evaluation_date')->nullable();
            $table->date('start_date')->nullable();
            $table->string('manager')->nullable();
            $table->decimal('administrative_fees', 10, 2)->nullable();
            $table->decimal('minimum_subscription', 10, 2)->nullable();
            $table->decimal('entry_fee', 10, 2)->nullable();
            $table->enum('risk_level', ['low', 'medium', 'high'])->nullable();
            $table->enum('advertisement_days', ['daily', 'monthly', 'weekly'])->nullable();
            $table->enum('evaluation_days', ['daily', 'monthly', 'weekly'])->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_funds');
    }
};
