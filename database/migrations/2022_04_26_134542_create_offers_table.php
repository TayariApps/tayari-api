<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('place_id')->nullable()->constrained();
            $table->foreignId('menu_id')->nullable()->constrained();
            $table->foreignId('juice_id')->nullable()->constrained();
            $table->foreignId('drink_id')->nullable()->constrained();
            $table->string('banner')->nullable();
            $table->float('current_price',8,2);
            $table->float('offer_percentage',8,2);
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->boolean('ongoing')->default(false);
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offers');
    }
}
