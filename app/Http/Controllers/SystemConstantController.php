<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Menu, SystemConstant};

class SystemConstantController extends Controller
{
    public function discountScript(){
        $constant = SystemConstant::where('id', 1)->first();

        $food = Menu::get();

        foreach ($food as $meal) {
            $meal->update([
                'discount' => 0.1
            ]);
        }

        return response()->json('Script complete',200);
    }
}
