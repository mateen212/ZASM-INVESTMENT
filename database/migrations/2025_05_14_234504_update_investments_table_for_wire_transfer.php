<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateInvestmentsTableForWireTransfer extends Migration
{
    public function up()
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->json('invoice_images')->nullable()->after('initiate_wire_transfer_date');
            $table->text('transaction_details')->nullable()->after('invoice_images');
            $table->string('wire_transfer_status')->default('Not Started')->after('transaction_details');
        });
    }

    public function down()
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->dropColumn(['invoice_images', 'transaction_details', 'wire_transfer_status']);
        });
    }
}