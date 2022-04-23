<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrinkDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'drink_id', 'delivery_id', 'quantity', 'price'
    ];
}
