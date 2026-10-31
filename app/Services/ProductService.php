<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $product = Product::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'unit' => $data['unit'],
                'weight' => $data['weight'],
            ]);

            $product->categories()->sync($data['categories']);
            $this->syncPrices($product, $data['prices']);
            $this->syncImages($product, $data['images'] ?? []);

            return $product;
        });
    }

    public function update(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $product->update([
                'name' => $data['name'],
                'description' => $data['description'],
                'unit' => $data['unit'],
                'weight' => $data['weight'],
            ]);

            $product->categories()->sync($data['categories']);

            if (!empty($data['prices'])) {
                $product->prices()->delete();
                $this->syncPrices($product, $data['prices']);
            }

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

    private function syncPrices(Product $product, array $prices): void
    {
        foreach ($prices as $priceData) {
            $attributeValueIds = collect($priceData['attribute_values'])
                ->map(fn ($id) => (int) $id)
                ->sort()
                ->values();
            $price = $product->prices()->create([
                'price' => $priceData['price'],
                'discount' => $priceData['discount'] ?? null,
                'inventory' => $priceData['inventory'],
            ]);
            $price->attributeValues()->sync($attributeValueIds->all());
        }
    }
}
