<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemConstant;

class SystemConstantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SystemConstant::create([
            'discount' => 0.10
        ]);
    }
}
