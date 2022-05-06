<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationDrinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservation_drinks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('drink_id')->constrained();
            $table->foreignId('reservation_id')->constrained();
            $table->integer('quantity');
            $table->float('cost',8,2);
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
        Schema::dropIfExists('reservation_drinks');
    }
}
