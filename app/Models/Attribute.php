<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $guarded = ['id'];

    public function values(){
        return $this->hasMany(AttributeValue::class);   
    }
}
