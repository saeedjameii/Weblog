<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompareList extends Model
{
    protected $table = 'compare_list';
    protected $guarded = ['id'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
