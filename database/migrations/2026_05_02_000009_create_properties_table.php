<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('properties')) {
            return;
        }

        Schema::create('properties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1)->index();
            $table->tinyInteger('is_active')->default(1)->index();
            $table->tinyInteger('running')->default(0)->index();
            $table->tinyInteger('completed')->default(0)->index();
            $table->decimal('price', 28, 8)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('properties')) {
            return;
        }

        Schema::dropIfExists('properties');
    }
};
