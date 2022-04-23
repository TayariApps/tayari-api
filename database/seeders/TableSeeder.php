<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Table;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Table::create([
            'place_id' => 1,
            'table_name' => 'Table 1'
        ]);

        Table::create([
            'place_id' => 1,
            'table_name' => 'Table 2'
        ]);
    }
}
