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
            'name' => 'Seafood',
            'image' => 'image'
        ]);

        Cuisine::create([
            'name' => 'Indian',
            'image' => 'image'
        ]);

        Cuisine::create([
            'name' => 'Ethiopean',
            'image' => 'image'
        ]);
    }
}
