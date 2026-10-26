<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;


class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ){}

    public function index(Request $request){
        $query = Product::with(['categories', 'images', 'prices.attributeValues'])->latest();

        if($request->filled('search')){
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%");
            });        
        }

        if($request->filled('category_id')){
            $query->whereHas('categories', function ($q) use ($request){
                $q->where('category_id', $request->category_id);
            });
        }

        $products = $query->paginate(8)->withQueryString();
        $categories = Category::forSelection();

        return view('products.index', compact('products', 'categories'));
    }

    public function show(Product $product){
        $product->load(['categories', 'images', 'prices.attributeValues']);
        return view('products.show', compact('product'));
    }

    public function panelIndex(Request $request){
        $query = Product::with(['categories', 'images', 'prices.attributeValues'])->latest();

        if($request->filled('search')){
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%");
        }

        if($request->filled('category_id')){
            $query->whereHas('categories', function ($q) use ($request){
                $q->where('category_id', $request->category_id);
            });
        }

        $products = $query->paginate(8)->withQueryString();
        $categories = Category::forSelection();

        return view('panel.products.index', compact('products', 'categories'));
    }

    public function create(){
        $categories = Category::forSelection();
        return view('panel.products.create', compact('categories'));
    }

    public function store(ProductRequest $request){
        $this->productService->create($request->validated());
        return redirect()->route('panel.products.index')->with('success', 'محصول با موفقیت ایجاد شد');
    }

    public function edit(Product $product){
        $categories = Category::forSelection();
        $product->load(['categories', 'images']);
        return view('panel.products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, Product $product){
        $this->productService->update($request->validated(), $product);
        return redirect()->route('panel.products.index')->with('success', 'محصول با موفقیت ویرایش شد');
    }

    public function destroy(Product $product){
        try{
            $product->delete();
            return redirect()->route('panel.products.index')->with('success', 'محصول با موفقیت حذف شد');
        }catch(QueryException $e){
            return redirect()->route('panel.products.index')->with('error', 'این محصول قابل حذف نمی‌باشد');
        }
    }

}
