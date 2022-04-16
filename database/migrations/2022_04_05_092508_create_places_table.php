<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('country_id')->constrained();
            $table->string('address');
            $table->foreignId('owner_id')->constrained('users');
            $table->string('logo_url');
            $table->string('banner_url');
            $table->string('policy_url');
            $table->string('phone_number');
            $table->string('email');
            $table->string('location');
            $table->string('latitude');
            $table->string('longitude');
            $table->text('description');
            $table->string('display_name');
            $table->string('cuisine');
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
        Schema::dropIfExists('places');
    }
}
