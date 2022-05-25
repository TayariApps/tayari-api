<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;

class OrderNumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $orders = Order::get();

        foreach ($orders as $order) {
            $order->update([
                'order_number' => "TYR-".$order->id
            ]);
        }
    }
}
