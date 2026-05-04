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
        Schema::create('personal_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deal_id');
            $table->foreign('deal_id')->references('id')->on('deals')->onDelete('cascade');
            $table->boolean('email_privacy_investor')->default(false);
            $table->boolean('email_interception_review')->default(false);
            $table->boolean('email_interception_sponser')->default(true);
            $table->string('notification_selected_sponser')->nullable();
            $table->boolean('document_visibility_investors')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_settings');
    }
};
