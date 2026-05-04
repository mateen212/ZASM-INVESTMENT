<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('support_tickets')) {
            return;
        }

        Schema::create('support_tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->text('reply')->nullable();
            $table->unsignedTinyInteger('status')->default(0)->index();
            $table->unsignedTinyInteger('priority')->default(0)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('support_tickets')) {
            return;
        }

        Schema::dropIfExists('support_tickets');
    }
};
