<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Type;
use App\Models\Place;
use App\Models\Menu;
use App\Models\SystemConstant;

class ResetDiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = Type::get();

        foreach ($types as $type) {
            $type->update([
                'discount' => 0.0
            ]);
        }

        $places = Place::get();

        foreach ($places as $place) {
            $place->update([
                'discount' => 0.0
            ]);
        }

        $menus = Menu::get();

        $constant = SystemConstant::where('id', 1)->first();

        foreach ($menus as $menu) {
            $menu->update([
                'discount' => 0.0 + $constant->discount
            ]);
        }
    }
}
