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
        Schema::table('assets', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->string('city')->nullable()->change();
            $table->string('state')->nullable()->change();
            $table->string('zip')->nullable()->change();
            $table->string('country')->nullable()->change();
            $table->string('property_type')->nullable()->change();
            $table->string('property_class')->nullable()->change();
            $table->string('number_of_units')->nullable()->change();
            $table->string('type_of_units')->nullable()->change();
            $table->string('year_built')->nullable()->change();
            $table->string('acquisition_date')->nullable()->change();
            $table->string('acquisition_price')->nullable()->change();
            $table->string('exit_date')->nullable()->change();
            $table->string('exit_price')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('name')->change();
            $table->string('address')->change();
            $table->string('city')->change();
            $table->string('state')->change();
            $table->string('zip')->change();
            $table->string('country')->change();
            $table->string('property_type')->change();
            $table->string('property_class')->change();
            $table->string('number_of_units')->change();
            $table->string('type_of_units')->change();
            $table->string('year_built')->change();
            $table->string('acquisition_date')->change();
            $table->string('acquisition_price')->change();
            $table->string('exit_date')->change();
            $table->string('exit_price')->change();
        });
    }
};
