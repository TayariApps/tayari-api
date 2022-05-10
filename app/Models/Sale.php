<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'delivery_id', 'code', 'amount', 
        'reference_id', 'remarks', 'type', 'phone_number', 'place_id', 'paid'
    ];

    public function order(){
        return $this->belongsTo(Order::class);
    }

    public function delivery(){
        return $this->belongsTo(Delivery::class);
    }

    public function place(){
        return $this->belongsTo(Place::class);
    }
}
