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
            'menu_name' => 'Chicken Salad',
            'description' => 'Chicken with cheese and vegetable salad',
            'size' => 'Large',
            'type_id' => 1,
            'place_id' => 1,
            'banner' => 'burgerimage.jpeg',
            'price' => 10000,
            'time_takes_to_make' => 15,
            'ingredients' => 'burger'
        ]);

        Menu::create([
            'menu_name' => 'Beef burger',
            'description' => 'Beef burger with cheese',
            'size' => 'Medium',
            'type_id' => 2,
            'banner' => 'burgerimage.jpeg',
            'price' => 10000,
            'place_id' => 1,
            'time_takes_to_make' => 15,
            'ingredients' => 'burger'
        ]);

        Menu::create([
            'menu_name' => 'Cheese burger',
            'description' => 'Just cheese in a burger',
            'size' => 'Medium',
            'type_id' => 2,
            'banner' => 'burgerimage.jpeg',
            'price' => 10000,
            'place_id' => 1,
            'time_takes_to_make' => 15,
            'ingredients' => 'burger'
        ]);
    }
}
