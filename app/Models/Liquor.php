<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Liquor extends Model
{
    use HasFactory;

    protected $fillable = [
        'place_id', 'drink_id','shots', 'price_per_shot'
    ];
}
