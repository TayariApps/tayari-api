<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Place;

class PlaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Place::create([
            'name' => 'Cape Town Fish Market',
            'country_id' => 1,
            'address' => 'Msasani',
            'owner_id' => 2,
            'policy_url' => 'https://tayari.co.tz/privacy',
            'phone_number' => '+255782835136',
            'email' => 'info@ctfm.com',
            'location' => 'Dar es Salaam',
            'latitude' => '200',
            'longitude' => '300',
            'description'=> 'Best seafood location in dar',
            'display_name' => 'CTFM',
            'cuisine_id' => 1,
            'banner_url' => 'banner',
            'logo_url' => 'logo'
        ]);

        Place::create([
            'name' => 'Samaki Samaki Masaki',
            'country_id' => 1,
            'address' => 'Masaki',
            'owner_id' => 2,
            'policy_url' => 'https://tayari.co.tz/privacy',
            'phone_number' => '+255782835136',
            'email' => 'info@samakisamaki.com',
            'location' => 'Dar es Salaam',
            'latitude' => '200',
            'longitude' => '300',
            'description'=> 'Best fishspot in dar',
            'display_name' => 'Samaki Masaki',
            'cuisine_id' => 1,
            'banner_url' => 'banner',
            'logo_url' => 'logo'
        ]);

        Place::create([
            'name' => 'Kukukuku',
            'country_id' => 1,
            'address' => 'Mlimani City',
            'owner_id' => 2,
            'policy_url' => 'https://tayari.co.tz/privacy',
            'phone_number' => '+255782835136',
            'email' => 'info@kukukuku.com',
            'location' => 'Dar es Salaam',
            'latitude' => '200',
            'longitude' => '300',
            'description'=> 'Best chicken spot in dar',
            'display_name' => 'Kukukuku',
            'cuisine_id' => 2,
            'banner_url' => 'banner',
            'logo_url' => 'logo'
        ]);


        Place::create([
            'name' => 'Cheif Kile',
            'country_id' => 1,
            'address' => 'Mbezi chini',
            'owner_id' => 2,
            'policy_url' => 'https://tayari.co.tz/privacy',
            'phone_number' => '+255782835136',
            'email' => 'info@cheifkile.com',
            'location' => 'Dar es Salaam',
            'latitude' => '200',
            'longitude' => '300',
            'description'=> 'Best food spot in dar',
            'display_name' => 'Cheif Kile',
            'cuisine_id' => 2,
            'banner_url' => 'banner',
            'logo_url' => 'logo'
        ]);
    }
}
