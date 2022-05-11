<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DrinkType;

class DrinkTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DrinkType::create([
            'name' => 'Soft Drinks' //1
        ]);

        DrinkType::create([
            'name' => 'Water' //2
        ]);

        DrinkType::create([
            'name' => 'Wines' //3
        ]);

        DrinkType::create([
            'name' => 'Beer' //4
        ]);

        DrinkType::create([
            'name' => 'Gin & Vodka' //5
        ]);

        DrinkType::create([
            'name' => 'Rum & Liqour' //6
        ]);

        DrinkType::create([
            'name' => 'Whiskey' //7
        ]);

        DrinkType::create([
            'name' => 'Brandy' //8
        ]);
    }   
}
