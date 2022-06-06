<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'disbursement_id', 'place_id', 'amount'
    ];

    public function orders(){
        return $this->hasMany(Order::class);
    }
}