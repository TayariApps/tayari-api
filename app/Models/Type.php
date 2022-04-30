<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function places(){
        return $this->belongsToMany(Place::class,'place_food_types','type_id','place_id')->using(PlaceFoodType::class);
    }

    public function menus(){
        return $this->hasMany(Menu::class,'type_id', 'id');
    }
}
