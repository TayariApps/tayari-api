<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantNumber extends Model
{
    use HasFactory;

    protected $fillable = [
        'place_id','phone','active'
    ];

    public function place(){
        return $this->belongsTo(Place::class);
    }
}