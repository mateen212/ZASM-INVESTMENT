<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('invests')) {
            return;
        }

        Schema::create('invests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->decimal('total_invest_amount', 28, 8)->default(0);
            $table->decimal('due_amount', 28, 8)->default(0);
            $table->decimal('total_profit', 28, 8)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('invests')) {
            return;
        }

        Schema::dropIfExists('invests');
    }
};
