<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users');
            $table->foreignId('order_created_by')->constrained('users');
            $table->integer('waiting_time');
            $table->bigInteger('excecuted_time');
            $table->float('cost', 8,2)->default(0.00);
            $table->float('total_cost', 8,2)->default(0.00);
            $table->integer('product_total')->default(0);
            $table->float('paid', 8,2)->default(0.00);
            $table->integer('discount_percentage')->default(0);
            $table->float('discount_value', 8,2)->nullable();
            $table->boolean('payment_status')->default(false);
            $table->integer('payment_method')->nullable();
            $table->foreignId('deliverer_id')->nullable()->constrained('users');
            $table->boolean('review_done')->default(false);
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
        Schema::dropIfExists('deliveries');
    }
}
