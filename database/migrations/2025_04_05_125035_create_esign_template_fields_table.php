<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateESignTemplateFieldsTable extends Migration
{
    public function up()
    {
        Schema::create('e_sign_templates_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('e_sign_templates_id');
            $table->string('type'); 
            $table->float('x'); 
            $table->float('y'); 
            $table->integer('page'); 
            $table->string('value')->nullable(); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('e_sign_templates_fields');
    }
}