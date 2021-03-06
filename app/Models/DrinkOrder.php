<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrinkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'drink_id', 'order_id', 'quantity', 'price'
    ];
}
