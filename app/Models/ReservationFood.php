<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationFood extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id', 'quantity', 'cost', 'reservation_id'
    ];

    public function menu(){
        return $this->belongsTo(Menu::class);
    }
}
