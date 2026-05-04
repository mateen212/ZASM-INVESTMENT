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
        //
        if (!Schema::hasTable('insights')) {
            return;
        }

        Schema::table('insights', function (Blueprint $table) {
            if (!Schema::hasColumn('insights', 'loan_assumption')) {
                $table->boolean('loan_assumption')->default(1)->after('loan_term'); // Replace 'some_column' with the column after which this new column should appear.
            }
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        if (!Schema::hasTable('insights')) {
            return;
        }

        Schema::table('insights', function (Blueprint $table) {
            if (Schema::hasColumn('insights', 'loan_assumption')) {
                $table->dropColumn('loan_assumption');
            }
        });
    }
};
