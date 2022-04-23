<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'waiting_time', 'executed_time', 'delivery_cost', 'cost',
        'total_cost', 'product_total', 'paid', 'discount_percentage',
        'discount_value', 'payment_status','deliverer_id','payment_method',
        'order_created_by','customer_id'
    ];
}
