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
        Schema::create('trx_channels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->string('payer_account_number')->nullable();
            $table->string('phone')->nullable();
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('agreement_id')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('trx_id')->nullable();
            $table->longText('bkash_res')->nullable();
            $table->enum('status', ['pending', 'failed', 'canclled', 'success'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_channels');
    }
};
