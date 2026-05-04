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
        Schema::create('buckets', function (Blueprint $table) {
            $table->id();
            $table->string('equity_bucket_name')->nullable();
            $table->decimal('raise_amount_ownership', 15, 2)->nullable();  
            $table->decimal('raise_amount_distributions', 15, 2)->nullable();  
            $table->integer('raise_quota')->nullable(); 
            $table->unsignedBigInteger('deal_id');

            // Define foreign key with deal table
            $table->foreign('deal_id')->references('id')->on('deals');
            $table->timestamps();
        });

        Schema::table('deal_classes', function (Blueprint $table) {
            $table->unsignedBigInteger('bucket_id')->after('deal_id')->nullable();
            $table->foreign('bucket_id')->references('id')->on('buckets');
        });
            
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bucket');
        Schema::table('deal_classes', function (Blueprint $table) {
            $table->dropForeign('deal_classes_bucket_id_foreign');
            $table->dropColumn('bucket_id');
        });
        

    }
};
