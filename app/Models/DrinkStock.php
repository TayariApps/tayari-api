<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrinkStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'place_id', 'drink_id', 'quantity', 'buying_price', 'selling_price'
    ];
    

}
