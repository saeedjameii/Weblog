<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::whereNull('parent_id')->with('children')->get();
        return view('categories.index', compact('categories'));
    }

    public function create(){

        $categories = Category::all();

        return view('categories.create', compact('categories'));
    }

    public function store(CategoryRequest $request){
        // dd($request->all());
        $data = $request->validated();

        Category::create($data);

        return redirect()->route('categories.index')->with('success', 'دسته‌بندی با موفقیت ایچاد شد');
    }

    public function edit(Category $category){
            // $categories = Category::where('id', '!=', $category->id)->get();
        $excludeId = $category->descendants()->pluck('id')->push($category->id);
        $categories = Category::whereNotIn('id', $excludeId)->get();


        return view('categories.edit',compact('category', 'categories'));
    }

    public function update(CategoryRequest $request, Category $category){
        $data = $request->validated();

        if(!empty($data['parent_id'])){
            $parent = Category::findOrFail($data['parent_id']);
            if(!$category->isValidParent($parent)){
                abort(422, 'این دسته بندی نمی‌تواند والد انتخاب شده باشد');
            }
        }

        $category->update($data);

        return redirect()->route('categories.index')->with('success', 'دسته‌بندی مورد نظر با موفقیت ویرایش شد');
    }

    public function destroy(Category $category){

        try{
            $category->delete();
            return redirect()->route('categories.index')->with('success', 'دسته‌بندی مورد نظر با موفقیت حذف شد');
        }
        catch(QueryException $e){
            return redirect()->route('categories.index')->with('error', 'این دسته‌بندی قابل حذف نیست؛ ابتدا پست‌های مربوط به آن را مدیریت کنید.');
        }
    }

}
