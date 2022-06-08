<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentCutToSystemConstantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('system_constants', function (Blueprint $table) {
            $table->float('payment_cut', 3,2)->default(0.02)->after('discount_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('system_constants', function (Blueprint $table) {
            $table->dropColumn('payment_cut');
        });
    }
}
