<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_id')->constrained();
            $table->integer('waiting_time');
            $table->bigInteger('excecuted_time');
            $table->float('delivery_cost', 8,2,)->default(0.00);
            $table->float('cost', 8,2);
            $table->float('total_cost', 8,2);
            $table->integer('product_total');
            $table->float('paid', 8,2)->default(0.00);
            $table->integer('discount_percentage')->default(0);
            $table->float('discount_value', 8,2)->nullable();
            $table->boolean('payment_status')->default(false);
            $table->foreignId('deliverer_id')->constrained('users')->nullable();
            $table->integer('payment_method')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
