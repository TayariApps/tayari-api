<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id', 'table_id', 'time', 'note', 'arrived'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function table(){
        return $this->belongsTo(Table::class);
    }
}


$table->foreignId('user_id')->constrained();
            $table->foreignId('table_id')->constrained();
            $table->dateTime('time');
            $table->text('note')->nullable();
            $table->boolean('arrived')->default(false);