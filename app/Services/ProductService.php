<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
	public function create(array $data): Product
	{
        return DB::transaction(function () use ($product, $data) {
            $product = Product::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'unit' => $data['unit'],
                'weight' => $data['weight'],
            ]);

            $product->categories()->sync($data['categories']);

            $this->syncImages($product, $data['images'] ?? []);

            return $product;
        });
	}

	public function update(Product $product, array $data): Product
	{
        return DB::transaction(function () use ($data) {
            $product->update([
                'name' => $data['name'],
                'description' => $data['description'],
                'unit' => $data['unit'],
                'weight' => $data['weight'],
            ]);

            $product->categories()->sync($data['categories']);

            if(!empty($data['images'])){
                foreach ($product->images as $image){
                    Storage::disk('public')->delete($image->path);
                }
                $product->images()->delete();
                $this->syncImages($product, $data['images']);
            }
            return $product;
        });
	}

    private function syncImages(Product $product, array $images): void
    {
        foreach ($images as $image) {
            $path = $image->store('products', 'public');
            $product->images()->create(['path' => $path]);
        }
    }
}
