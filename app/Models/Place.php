<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use HasFactory;
    protected $placeId;

    protected $fillable = [
        'name', 'country_id', 'address','owner_id','logo_url','place_discount',
        'banner_url', 'policy_url', 'phone_number','email','active','cashier_number',
        'location','latitude','longitude','description','display_name','cuisine_id',
        'account_name', 'account_number', 'bank_swift_code', 'bank_name','discount','is_open',
        'payment_number', 'payment_network', 'payment_name'
    ];

    public function schedules(){
        return $this->hasMany(Schedule::class);
    }

    public function user(){
        return $this->belongsTo(User::class,'owner_id', 'id');
    }

    public function sales(){
        return $this->hasMany(Sale::class);
    }

    public function menus(){
        return $this->hasMany(Menu::class);
    }

    public function disbursements(){
        return $this->hasMany(Disbursement::class);
    }

    public function cuisine(){
        return $this->belongsTo(Cuisine::class);
    }

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function tables(){
        return $this->hasMany(Table::class);
    }

    public function types(){
        return $this->hasMany(Type::class);
    }

    public function employees(){
        return $this->hasMany(Employee::class);
    }

    public function orders(){
        return $this->hasMany(Order::class);
    }

    public function drinks(){
        return $this->belongsToMany(Drink::class,'drink_stocks','place_id','drink_id')->withPivot('quantity', 'buying_price', 'selling_price');
    }

    public function reviewed(){
        return $this->hasMany(Review::class,'place_id','id');
    }

    public function reviews(){
        return $this->belongsToMany(User::class,'reviews','place_id','user_id')->withPivot('content','rating','status','is_anonymous');
    }

}