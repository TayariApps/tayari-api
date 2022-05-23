<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    use HasFactory;

    protected $fillable = [
        'disbursement_id', 'amount', 'place_id'
    ];

    public function disbursement(){
        return $this->belongsTo(Disbursement::class);
    }

    public function place(){
        return $this->belongsTo(Place::class);
    }
}
