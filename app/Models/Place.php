<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'country_id', 'address','owner_id','logo_url',
        'banner_url', 'policy_url', 'phone_number','email',
        'location','latitude','longitude','description','display_name','cuisine_id'
    ];

    public function user(){
        return $this->belongsTo(User::class,'owner_id', 'id');
    }

    public function menus(){
        return $this->hasMany(Menu::class);
    }

    public function cuisine(){
        return $this->belongsTo(Cuisine::class);
    }

    public function tables(){
        return $this->hasMany(Table::class);
    }

}