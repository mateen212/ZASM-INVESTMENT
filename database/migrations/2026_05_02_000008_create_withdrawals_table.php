<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('withdrawals')) {
            return;
        }

        Schema::create('withdrawals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('trx')->nullable()->index();
            $table->string('method')->nullable();
            $table->decimal('amount', 28, 8)->default(0);
            $table->decimal('charge', 28, 8)->default(0);
            $table->tinyInteger('status')->default(0)->index();
            $table->text('detail')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('withdrawals')) {
            return;
        }

        Schema::dropIfExists('withdrawals');
    }
};
