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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->string('wallet_trx_id')->primary();
            $table->string('wallet_id');
            $table->foreign('wallet_id')->references('wallet_id')->on('wallets')->onDelete('cascade');
            $table->enum('type', ['pending', 'success', 'failed', 'cancelled'])->default('pending');
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('payment_id')->nullable();
            $table->string('trx_id')->nullable();
            $table->longText('bkash_res')->nullable();
            $table->enum('status', ['pending', 'success'])->default('pending');
            $table->timestamps();
            $table->index(['wallet_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
