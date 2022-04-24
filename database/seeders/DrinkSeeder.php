<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Drink;

class DrinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Drink::create([
            'name' => 'Fanta Orange', 
            'volume' => 350, 
            'image' => 'image', 
            'type' => 1
        ]);

        Drink::create([
            'name' => 'Coca Cola', 
            'volume' => 350, 
            'image' => 'image', 
            'type' => 1
        ]);

        Drink::create([
            'name' => 'Pepsi', 
            'volume' => 350, 
            'image' => 'image', 
            'type' => 1
        ]);
    }
}
