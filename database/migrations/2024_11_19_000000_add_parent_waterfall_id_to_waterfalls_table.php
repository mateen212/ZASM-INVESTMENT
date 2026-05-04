
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParentWaterfallIdToWaterfallsTable extends Migration
{
    public function up()
    {
        Schema::table('waterfalls', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_waterfall_id')->nullable();
            $table->foreign('parent_waterfall_id')->references('id')->on('waterfalls')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('waterfalls', function (Blueprint $table) {
            $table->dropForeign(['parent_waterfall_id']);
            $table->dropColumn('parent_waterfall_id');
        });
    }
}