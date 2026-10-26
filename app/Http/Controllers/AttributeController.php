<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttributeRequest;
use App\Models\Attribute;

class AttributeController extends Controller
{
    public function index(){
        $attributes = Attribute::with('values')->latest()->get();
        return view('panel.attributes.index', compact('attributes'));
    }

    public function create(){
        return view('panel.attributes.create');
    }

    public function store(AttributeRequest $request){
        $data = $request->validated();

        Attribute::create($data);

        return redirect()->route('panel.attributes.index')->with('success', 'ویژگی با موفقیت ایچاد شد');
    }

    public function edit(Attribute $attribute){
        return view('panel.attributes.edit', compact('attribute'));
    }

    public function update(AttributeRequest $request, Attribute $attribute){
        $data = $request->validated();

        $attribute->update($data);

        return redirect()->route('panel.attributes.index')->with('success', 'ویژگی مورد نظر با موفقیت ویرایش شد');
    }

    public function destroy(Attribute $attribute){
        $attribute->delete();
        return redirect()->route('panel.attributes.index')->with('success', 'ویژگی مورد نظر با موفقیت حذف شد');
    }

    
}
