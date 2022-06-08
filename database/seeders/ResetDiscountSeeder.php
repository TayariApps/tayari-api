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
        $constant = SystemConstant::where('id', 1)->first();
        $places = Place::get();
        $menus = Menu::get();

        foreach ($types as $type) {
            $type->update([
                'discount' => $constant->discount,
                'type_discount' => 0.0
            ]);
        }

        foreach ($places as $place) {
            $place->update([
                'discount' => $constant->discount,
                'place_discount' => 0.0
            ]);
        }

        foreach ($menus as $menu) {
            $menu->update([
                'discount' => $constant->discount,
                'food_discount' => 0.0
            ]);
        }
    }
}
