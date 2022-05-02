<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Help extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'place_id', 'message', 'is_read'
    ];
}
