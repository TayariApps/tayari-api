<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemConstant extends Model
{
    use HasFactory;

    protected $fillable = [
        'discount', 'discount_active', 'payment_cut'
    ];
}
