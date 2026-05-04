<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealsTable extends Migration
{
    public function up()
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->id();  
            
            $table->string('name');  
            $table->string('type');   
            $table->enum('deal_stage', ['Raising capital', 'Asset managing', 'Liquidated']);  
            $table->string('sec_type');     
            $table->date('close_date')->nullable();  
            $table->string('owning_entity_name');  
            $table->boolean('funds_received_before_gp_countersigns')->default(false);  
            $table->boolean('send_funding_instructions_after_gp_countersigns')->default(false);

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->timestamps(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('deals'); // Drops the deals table if it exists
    }
}
