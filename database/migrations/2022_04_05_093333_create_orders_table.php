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
            $table->foreignId('table_id')->nullable()->constrained();
            $table->foreignId('customer_id')->nullable()->constrained('users');
            $table->foreignId('place_id')->constrained();
            $table->foreignId('order_created_by')->constrained('users');
            $table->integer('waiting_time');
            $table->bigInteger('executed_time');
            $table->dateTime('completed_time');
            $table->float('cost', 8,2)->default(0.00);
            $table->float('total_cost', 8,2)->default(0.00);
            $table->integer('product_total')->default(0);
            $table->float('paid', 8,2)->default(0.00);
            $table->integer('discount_percentage')->default(0);
            $table->float('discount_value', 8,2)->nullable();
            $table->boolean('payment_status')->default(false);
            $table->integer('payment_method')->nullable(); //1 -->cash, 2-->mobile, 3-->card , 4-->
            $table->boolean('has_offer')->default(false);
            $table->boolean('review_done')->default(false);
            $table->integer('type'); //1--> pre-order, 2--> dine-in , 3-->reservation //4-->delivery
            $table->integer('status')->default(1);
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
