<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id', 'user_id', 'content', 'rating', 'status', 'is_anonymous'
    ];

    public function menu(){
        return $this->belongsTo(Menu::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
