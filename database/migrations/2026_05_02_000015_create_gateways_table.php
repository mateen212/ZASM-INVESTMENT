<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('gateways')) {
            return;
        }

        Schema::create('gateways', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('code')->nullable()->index();
            $table->text('gateway_parameters')->nullable();
            $table->tinyInteger('status')->default(1)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('gateways')) {
            return;
        }

        Schema::dropIfExists('gateways');
    }
};
