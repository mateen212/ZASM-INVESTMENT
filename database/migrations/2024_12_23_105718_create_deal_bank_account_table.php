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
        Schema::create('deal_bank_account', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deal_id')->nullable();
            $table->string('account_nick_name')->nullable();
            $table->string('account_type')->nullable();
            $table->string('ach_account_number')->nullable();
            $table->string('account_number')->nullable();
            $table->string('check_signature')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deal_bank_account');
    }
};
