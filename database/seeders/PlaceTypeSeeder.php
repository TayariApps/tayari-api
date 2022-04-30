<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlaceFoodType;

class PlaceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PlaceFoodType::create([
            'place_id' => 1,
            'type_id' => 2
        ]);
    }
}
