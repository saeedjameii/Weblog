<?php

namespace App\Http\Controllers;

use App\Http\Requests\PriceRequest;
use App\Models\Price;

class PriceController extends Controller
{
    public function store(PriceRequest $request){
        $data = $request->validated();

        Price::create($data);

        return redirect()->route('panel.products.index')->with('success', 'قیمت موردنظر با موفقیت ایجاد شد');
    }

    public function update(PriceRequest $request, Price $price){
        $data = $request->validated();

        $price->update($data);

        return redirect()->route('panel.products.index')->with('success', 'قیمت موردنظر با موفقیت ویرایش شد');
    }

    public function destroy(Price $price){
        $price->delete();
        return redirect()->route('panel.products.index')->with('success', 'قیمت موردنظر با موفقیت حذف شد');
    }
} 
