
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsToOfferingsTable extends Migration
{
    public function up()
    {
        Schema::table('offerings', function (Blueprint $table) {
            $table->string('video_url')->nullable()->after('internal_name');
            $table->json('overview_metrics')->nullable()->after('video_url');
            $table->longText('summary')->nullable()->after('overview_metrics');
        });
    }

    public function down()
    {
        Schema::table('offerings', function (Blueprint $table) {
            $table->dropColumn(['video_url', 'overview_metrics', 'summary']);
        });
    }
}