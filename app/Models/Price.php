<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::saving(function (Price $price) {
            $discount = $price->discount ?? 0;

            $price->final_price = $discount > 0 ? $price->price - ($price->price * $discount / 100) : $price->price;
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'price_attribute_value');
    }
}
