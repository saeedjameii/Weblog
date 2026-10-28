<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttributeRequest;
use App\Models\Attribute;

class AttributeController extends Controller
{
    public function index(){
        $attributes = Attribute::with(['values' => fn ($query) => $query->withCount('prices')])->latest()->get();
        return view('panel.attributes.index', compact('attributes'));
    }

    public function create(){
        return view('panel.attributes.create');
    }

    public function store(AttributeRequest $request){
        $data = $request->validated();

        Attribute::create($data);

        return redirect()->route('panel.attributes.index')->with('success', 'ویژگی با موفقیت ایجاد شد. حالا مقدارهای آن را اضافه کنید.');
    }

    public function edit(Attribute $attribute){
        $attribute->load(['values' => fn ($query) => $query->withCount('prices')]);

        return view('panel.attributes.edit', compact('attribute'));
    }

    public function update(AttributeRequest $request, Attribute $attribute){
        $data = $request->validated();

        $attribute->update($data);

        return redirect()->route('panel.attributes.edit', $attribute)->with('success', 'ویژگی با موفقیت ویرایش شد.');
    }

    public function destroy(Attribute $attribute)
    {
        if ($attribute->values()->whereHas('prices')->exists()) {
            return back()->withErrors(['attribute' => 'این ویژگی در تنوع‌های محصولات استفاده شده و قابل حذف نیست.']);
        }

        $attribute->delete();

        return redirect()->route('panel.attributes.index')->with('success', 'ویژگی با موفقیت حذف شد.');
    }


}
