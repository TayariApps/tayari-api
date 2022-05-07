<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id', 'table_id', 'time', 'note', 'arrived','place_id','people_count'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function table(){
        return $this->belongsTo(Table::class);
    }

    public function place(){
        return $this->belongsTo(Place::class);
    }

    public function food(){
        return $this->hasMany(ReservationFood::class);
    }

    public function drinks(){
        return $this->hasMany(ReservationDrink::class);
    }

}