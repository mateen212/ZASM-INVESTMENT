<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('frontends')) {
            Schema::create('frontends', function (Blueprint $table) {
                $table->id();
                $table->string('tempname')->index();
                $table->string('data_keys')->index();
                $table->string('slug')->nullable()->index();
                $table->json('data_values')->nullable();
                $table->json('seo_content')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('frontends')) {
            Schema::dropIfExists('frontends');
        }
    }
};
