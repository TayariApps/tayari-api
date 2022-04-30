<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlaceFoodType extends Model
{
    use HasFactory;

    protected $fillable = [
        'place_id', 'type_id'
    ];

    public function type(){
        return $this->belongsTo(Type::class);
    }
}
