<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_id', 'executed_time', 'cost', 'total_cost','product_total','paid',
        'discount_percentage', 'disount_value', 'payment_status', 'payment_method', 'customer_id',
        'order_created_by','waiting_time'
    ];

    public function table(){
        return $this->belongsTo(Table::class);
    }

    public function food(){
        return $this->belongsToMany(Menu::class,'order_items','order_id','menu_id')->withPivot('quantity', 'cost');
    }

    public function drinks(){
        return $this->belongsToMany(Drink::class,'drink_orders','order_id','drink_id')->withPivot('quantity','price');
    }
}
