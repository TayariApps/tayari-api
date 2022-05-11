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
            'name' => 'Kevin Misigaro', 
            'email' => 'kunbata93@gmail.com',
            'role' => 3,
            'phone' => '+255782835136',
            'country_id' => 1,
        ]);
    }
}
