<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_name', 'description', 'size', 'type_id', 'banner', 'food_discount',
        'price', 'time_takes_to_make','place_id','ingredients','status','discount','kilos'
    ];

    public function type(){
        return $this->belongsTo(Type::class);
    }

    public function place(){
        return $this->belongsTo(Place::class);
    }

    public function reviews(){
        return $this->belongsToMany(User::class,'food_reviews','menu_id','user_id')->withPivot('content','rating','status', 'is_anonymous');
    }

    public function orders(){
        return $this->hasMany(OrderItem::class);
    }

}
