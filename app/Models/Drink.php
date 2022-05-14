<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drink extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'volume', 'image', 'type_id'
    ];

    public function places(){
        return $this->belongsToMany(Place::class,'drink_stocks','drink_id','place_id')->withPivot('quantity', 'buying_price', 'selling_price');
    }

    public function stocks(){
        return $this->hasOne(DrinkStock::class);
    }

    public function orders(){
        return $this->hasMany(DrinkOrder::class);
    }
}

