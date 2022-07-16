<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'place_id', 'discount','type_discount', 'status', 'drink_type_id', 'addon'
    ];

    public function places(){
        return $this->belongsTo(Place::class);
    }

    public function menus(){
        return $this->hasMany(Menu::class);
    }

    public function drinkType(){
        return $this->belongsTo(DrinkType::class);
    }
}
