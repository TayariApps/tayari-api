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
            $table->boolean('active')->default(false);
            $table->string('address');
            $table->foreignId('owner_id')->constrained('users');
            $table->string('logo_url');
            $table->string('banner_url');
            $table->string('policy_url')->nullable();
            $table->string('phone_number');
            $table->string('email');
            $table->string('location');
            $table->string('latitude');
            $table->string('longitude');
            $table->text('description');
            $table->string('display_name');
            $table->string('opening_time')->nullable();
            $table->string('closing_time')->nullable();
            $table->foreignId('cuisine_id')->constrained();
            $table->boolean('reservation_payable')->default(false);
            $table->integer('reservation_price')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('bank_swift_code')->nullable();
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
