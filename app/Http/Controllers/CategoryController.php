<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostCategoryRequest;
use App\Http\Requests\ProductCategoryRequest;
use App\Models\Category;
use Illuminate\Database\QueryException;

class CategoryController extends Controller
{
    public function postIndex()
    {
        $categories = Category::treeRootsForType('post');
        return view('categories.post.index', compact('categories'));
    }

    public function productIndex()
    {
        $categories = Category::treeRootsForType('product');
        return view('categories.product.index', compact('categories'));
    }

    public function postPanelIndex(){
        $categories = Category::treeRootsForType('post');
        return view('panel.categories.post.index', compact('categories'));
    }

    public function productPanelIndex(){
        $categories = Category::treeRootsForType('product');
        return view('panel.categories.product.index', compact('categories'));
    }

    public function createPost(){
        $categories = Category::forSelection('post');
        return view('panel.categories.post.create', compact('categories'));
    }

    public function createProduct(){
        $categories = Category::forSelection('product');
        return view('panel.categories.product.create', compact('categories'));
    }

    public function storePost(PostCategoryRequest $request){
        $data = $request->validated();
        Category::create($data);
        return redirect()->route('panel.categories.posts.index')->with('success', 'دسته‌بندی با موفقیت ایجاد شد');
    }

    public function storeProduct(ProductCategoryRequest $request){
        $data = $request->validated();
        Category::create($data);
        return redirect()->route('panel.categories.products.index')->with('success', 'دسته‌بندی با موفقیت ایجاد شد');
    }

    public function editPost(Category $category){
        abort_unless($category->type === 'post', 404);
        $categories = Category::forSelectionExceptSubtree($category);
        return view('panel.categories.post.edit',compact('category', 'categories'));
    }

    public function editProduct(Category $category){
        abort_unless($category->type === 'product', 404);
        $categories = Category::forSelectionExceptSubtree($category);
        return view('panel.categories.product.edit',compact('category', 'categories'));
    }

    public function updatePost(PostCategoryRequest $request, Category $category){
        abort_unless($category->type === 'post', 404);
        $data = $request->validated();
        $category->update($data);
        return redirect()->route('panel.categories.posts.index')->with('success', 'دسته‌بندی مورد نظر با موفقیت ویرایش شد');
    }

    public function updateProduct(ProductCategoryRequest $request, Category $category){
        abort_unless($category->type === 'product', 404);
        $data = $request->validated();
        $category->update($data);
        return redirect()->route('panel.categories.products.index')->with('success', 'دسته‌بندی مورد نظر با موفقیت ویرایش شد');
    }

    public function destroy(Category $category){

        if($category->type === 'post'){
            $route = 'panel.categories.posts.index';
        }
        else{
            $route = 'panel.categories.products.index';
        }
        try{
            $category->delete();
            return redirect()->route($route)->with('success', 'دسته‌بندی مورد نظر با موفقیت حذف شد');
        }
        catch(QueryException $e){
            return redirect()->route($route)->with('error', 'این دسته‌بندی قابل حذف نیست؛ ابتدا موارد وابسته به آن را مدیریت کنید.');
        }
    }

}
