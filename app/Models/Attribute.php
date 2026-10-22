<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    public function Values(){
        return $this->hasMany(AttributeValue::class);   
    }
}
