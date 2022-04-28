<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'place_id', 'table_name'
    ];

    public function place(){
        return $this->belongsTo(Place::class);
    }

    public function orders(){
        return $this->hasMany(Order::class);
    }

}
