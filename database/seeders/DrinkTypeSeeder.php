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
            'name' => 'soda'
        ]);

        DrinkType::create([
            'name' => 'water'
        ]);

        DrinkType::create([
            'name' => 'local beer'
        ]);

        DrinkType::create([
            'name' => 'imported beer'
        ]);
    }   
}
