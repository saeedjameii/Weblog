<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = ['id'];

    public function categories(){
        return $this->belongsToMany(Category::class);
    }

    public function images(){
        return $this->morphMany(Image::class, 'imageable');
    }

    public function prices(){
        return $this->hasMany(Price::class);
    }

}
