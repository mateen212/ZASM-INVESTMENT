<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('profits')) {
            return;
        }

        Schema::create('profits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->decimal('amount', 28, 8)->default(0);
            $table->unsignedTinyInteger('status')->default(0)->index();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('profits')) {
            return;
        }

        Schema::dropIfExists('profits');
    }
};
