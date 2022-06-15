<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserCoupon;
use Illuminate\Support\Str;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0; $i < 26; $i++) { 
            UserCoupon::create([
                'coupon' => Str::upper(Str::random(12))
            ]);
        }
    }
}
