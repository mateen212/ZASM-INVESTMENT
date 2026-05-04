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
        Schema::create('offerings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('internal_name')->nullable();
            $table->double('offering_size', 15, 2)->nullable();
            $table->tinyInteger('status')->default(0)->comment('0: Draft, 1: Active, 2: Inactive');
            $table->string('visibility')->nullable();
            $table->bigInteger('deal_id')->unsigned();
            $table->foreign('deal_id')->references('id')->on('deals')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('offering_media', function (Blueprint $table) {
            $table->id();
            $table->string('media_type')->nullable();
            $table->string('media_url')->nullable();
            $table->bigInteger('offering_id')->unsigned();
            $table->foreign('offering_id')->references('id')->on('offerings')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('deal_class_offering', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('deal_class_id')->unsigned();
            $table->foreign('deal_class_id')->references('id')->on('deal_classes')->onDelete('cascade');
            $table->bigInteger('offering_id')->unsigned();
            $table->foreign('offering_id')->references('id')->on('offerings')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('asset_offering', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('asset_id')->unsigned();
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->bigInteger('offering_id')->unsigned();
            $table->foreign('offering_id')->references('id')->on('offerings')->onDelete('cascade');
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_offering');
        Schema::dropIfExists('deal_class_offering');
        Schema::dropIfExists('offering_media');
        Schema::dropIfExists('offerings');
    }
};
