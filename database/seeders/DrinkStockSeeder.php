<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DrinkStock;

class DrinkStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DrinkStock::create([
            'place_id' => 1, 
            'drink_id' => 1, 
            'quantity' => 10, 
            'buying_price' => 400, 
            'selling_price' => 1000
        ]);

        DrinkStock::create([
            'place_id' => 1, 
            'drink_id' => 2, 
            'quantity' => 10, 
            'buying_price' => 400, 
            'selling_price' => 1000
        ]);

        DrinkStock::create([
            'place_id' => 1, 
            'drink_id' => 3, 
            'quantity' => 10, 
            'buying_price' => 400, 
            'selling_price' => 1000
        ]);
    }
}
