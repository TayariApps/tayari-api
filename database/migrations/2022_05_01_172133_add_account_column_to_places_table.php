<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccountColumnToPlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('places', function (Blueprint $table) {
            $table->string('account_name')->nullable()->after('reservation_price');
            $table->string('account_number')->nullable()->after('account_name');
            $table->string('bank_swift_code')->nullable()->after('account_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('places', function (Blueprint $table) {
            $table->dropColumn('account_name');
            $table->dropColumn('account_number');
            $table->dropColumn('bank_swift_code');
        });
    }
}
