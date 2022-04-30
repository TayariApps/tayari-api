<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'place_id'
    ];

    public function place(){
        return $this->belongsTo(Place::class);
    }

    public function menus(){
        return $this->hasMany(Menu::class);
    }
}