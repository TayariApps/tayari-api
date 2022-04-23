<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@tayari.co.tz',
            'role' => 1
        ]);

        User::create([
            'name' => 'Samweli Abdallah', 
            'email' => 'thetrues.live.co.uk',
            'role' => 3,
            'phone' => '+255712395987',
            'country_id' => 1,
        ]);
    }
}
