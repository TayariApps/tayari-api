<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCoupon extends Model
{
    use HasFactory;

    protected $fillable =[
        'coupon', 'user_id', 'used'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
