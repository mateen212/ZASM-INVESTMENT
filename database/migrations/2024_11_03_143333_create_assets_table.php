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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('zip');
            $table->string('country');
            $table->string('property_type');
            $table->string('property_class');
            $table->string('number_of_units');
            $table->string('type_of_units');
            $table->string('year_built');
            $table->string('acquisition_date');
            $table->string('acquisition_price');
            $table->string('exit_date');
            $table->string('exit_price');

            $table->foreignId('deal_id')->constrained()->onDelete('cascade');

            $table->timestamps();
        });

        Schema::create('asset_media', function (Blueprint $table) {
            
            $table->id();
            $table->string('media_url');
            $table->string('media_type');
            $table->string('media_description');
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_media');
        Schema::dropIfExists('assets');
    }
};
