<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttributeValueRequest;
use App\Models\AttributeValue;

class AttributeValueController extends Controller
{
    public function store(AttributeValueRequest $request){
        $data = $request->validated();

        AttributeValue::create($data);

        return redirect()->route('panel.attributes.edit', $data['attribute_id'])->with('success', 'مقدار ویژگی با موفقیت ایجاد شد.');
    }

    public function update(AttributeValueRequest $request, AttributeValue $attributeValue){
        $data = $request->validated();

        if ($attributeValue->prices()->exists() && (int) $data['attribute_id'] !== (int) $attributeValue->attribute_id) {
            return back()->withErrors(['attribute_id' => 'این مقدار در تنوع محصول استفاده شده و نمی‌توان ویژگی آن را تغییر داد.']);
        }

        $attributeValue->update($data);

        return redirect()->route('panel.attributes.edit', $attributeValue->attribute_id)->with('success', 'مقدار ویژگی با موفقیت ویرایش شد.');
    }

    public function destroy(AttributeValue $attributeValue){
        if ($attributeValue->prices()->exists()) {
            return back()->withErrors(['attribute_value' => 'این مقدار در تنوع‌های محصولات استفاده شده و قابل حذف نیست.']);
        }

        $attributeId = $attributeValue->attribute_id;
        $attributeValue->delete();

        return redirect()->route('panel.attributes.edit', $attributeId)->with('success', 'مقدار ویژگی با موفقیت حذف شد.');
    }
}
