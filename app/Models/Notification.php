<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'body', 'user_id','fcm','readed', 'unix_time'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}

