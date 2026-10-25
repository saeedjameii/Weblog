<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use hasFactory;
    use SoftDeletes;

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
