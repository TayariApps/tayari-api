<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Country::create([
            'name' => 'Tanzania',
            'currency' => 'TZS',
            'rate' => 0.00043
        ]);

        Country::create([
            'name' => 'Kenya',
            'currency' => 'KES',
            'rate' => 0.0092
        ]);

        Country::create([
            'name' => 'Uganda',
            'currency' => 'UGX',
            'rate' => 0.00028
        ]);
    }
}
