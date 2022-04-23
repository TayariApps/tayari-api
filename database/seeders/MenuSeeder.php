<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Menu::create([
            'place_id' => 1,
            'menu_name' => 'Chicken Salad',
            'description' => 'Chicken with cheese and vegetable salad',
            'size' => 'Large',
            'type' => 1,
            'banner' => 'banner',
            'price' => 10000,
            'time_takes_to_make' => 15
        ]);

        Menu::create([
            'place_id' => 1,
            'menu_name' => 'Beef burger',
            'description' => 'Beef burger with cheese',
            'size' => 'Medium',
            'type' => 1,
            'banner' => 'banner',
            'price' => 10000,
            'time_takes_to_make' => 15
        ]);

        Menu::create([
            'place_id' => 1,
            'menu_name' => 'Cheese burger',
            'description' => 'Just cheese in a burger',
            'size' => 'Medium',
            'type' => 1,
            'banner' => 'banner',
            'price' => 10000,
            'time_takes_to_make' => 15
        ]);
    }
}
