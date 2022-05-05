<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cuisine;

class CuisineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cuisine::create([
            'name' => 'Swahili & African',
            'image' => 'image'
        ]);

        Cuisine::create([
            'name' => 'Far East Cuisine',
            'image' => 'image'
        ]);

        Cuisine::create([
            'name' => 'Asian Cuisine',
            'image' => 'image'
        ]);

        Cuisine::create([
            'name' => 'European Cuisine',
            'image' => 'image'
        ]);

        Cuisine::create([
            'name' => 'Fast Foods',
            'image' => 'image'
        ]);

        Cuisine::create([
            'name' => 'Cafes & Pastries',
            'image' => 'image'
        ]);
    }
}
