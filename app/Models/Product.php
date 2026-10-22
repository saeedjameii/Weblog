<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = ['id'];

    public function categories(){
        return $this->belongsToMany(Category::class);
    }

    // public function users(){
    //     return $this->belongsTo(User::class);
    // }

    public function images(){
        return $this->morphMany(Image::class, 'imageable');
    }

    public function prices(){
        return $this->hasMany(Price::class);
    }

    public function attributes(){
        return $this->belongsToMany(Attribute::class, 'attribute_values');
    }


}
