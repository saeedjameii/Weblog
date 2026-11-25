<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

class Order extends Model
{
    protected $guarded = ['id'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function items(){
        return $this->hasMany(OrderItem::class);
    }

    public function getCreatedAtJalaliAttribute(): string
    {
        return $this->created_at ? Jalalian::fromCarbon($this->created_at)->format('Y/m/d ساعت H:i') : '';
    }
}

