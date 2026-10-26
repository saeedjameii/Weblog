<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttributeValueRequest;
use App\Models\AttributeValue;

class AttributeValueController extends Controller
{
    public function store(AttributeValueRequest $request){
        $data = $request->validated();

        AttributeValue::create($data);

        return redirect()->route('panel.attributes.index')->with('success', 'مقدار ویژگی با موفقیت ایجاد شد');
    }

    public function update(AttributeValueRequest $request, AttributeValue $attributeValue){
        $data = $request->validated();

        $attributeValue->update($data);

        return redirect()->route('panel.attributes.index')->with('success', 'مقدار ویژگی مورد نظر با موفقیت ویرایش شد');
    }

    public function destroy(AttributeValue $attributeValue){
        $attributeValue->delete();
        return redirect()->route('panel.attributes.index')->with('success', 'مقدار ویژگی مورد نظر با موفقیت حذف شد');
    }
}
