<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_name', 'description', 'size', 'type', 'banner',
        'price', 'time_takes_to_make', 'place_id'
    ];

    public function place(){
        return $this->belongsTo(Place::class);
    }

}
