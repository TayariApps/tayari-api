<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationDrink extends Model
{
    use HasFactory;

    protected $fillable = [
        'drink_id', 'reservation_id', 'quantity', 'cost'
    ];

}
