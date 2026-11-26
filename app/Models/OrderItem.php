<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $guarded = ['id'];

    public function order(){
        return $this->belongsTo(Order::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function price(){
        return $this->belongsTo(Price::class);
    }

    public function getColorNameAttribute(): ?string
    {
        if ($this->relationLoaded('price') && $this->price?->relationLoaded('attributeValues')) {
            $colorVal = $this->price->attributeValues->first(function ($val) {
                return ($val->attribute?->name === 'رنگ') || ($val->attribute?->type === 'color');
            });
            if ($colorVal) {
                return $colorVal->value;
            }
        }

        if ($this->variant_name && preg_match('/رنگ:\s*([^|]+)/u', $this->variant_name, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    public function getOtherVariantsAttribute(): ?string
    {
        if (! $this->variant_name) {
            return null;
        }

        $parts = array_filter(array_map('trim', explode('|', $this->variant_name)), function ($part) {
            return ! str_starts_with($part, 'رنگ:');
        });

        return ! empty($parts) ? implode(' | ', $parts) : null;
    }
}
