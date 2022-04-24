<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Type;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Type::create([
            'name' => 'Salads',
            'place_id' => 1
        ]); 
        
        Type::create([
            'name' => 'Burgers',
            'place_id' => 1
        ]); 
    }
}
