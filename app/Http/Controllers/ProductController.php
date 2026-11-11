<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\CompareList;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ){}

    public function index(Request $request){
        $query = Product::where('status', 'available')->with(['categories', 'images', 'prices.attributeValues'])->latest();

        if($request->filled('search')){
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%");
            });        
        }

        if($request->filled('category_id')){
            $query->whereHas('categories', function ($q) use ($request){
                $q->where('categories.id', $request->category_id)->where('categories.type', 'product');
            });
        }

        $products = $query->paginate(8)->withQueryString();
        $categories = Category::forSelection('product');

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
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%");
            });  
        }

        if($request->filled('category_id')){
            $query->whereHas('categories', function ($q) use ($request){
                $q->where('categories.id', $request->category_id)->where('categories.type', 'product');
            });
        }

        $products = $query->paginate(8)->withQueryString();
        $categories = Category::forSelection('product');

        return view('panel.products.index', compact('products', 'categories'));
    }

    public function create(){
        $categories = Category::forSelection('product');
        $attributes = Attribute::with('values')->orderBy('name')->get();
        return view('panel.products.create', compact('categories', 'attributes'));
    }

    public function store(ProductRequest $request){
        $this->productService->create($request->validated());
        return redirect()->route('panel.products.index')->with('success', 'محصول با موفقیت ایجاد شد');
    }

    public function edit(Product $product){
        $categories = Category::forSelection('product');
        $attributes = Attribute::with('values')->orderBy('name')->get();
        $product->load(['categories', 'images', 'prices.attributeValues']);

        $existingPrices = $product->prices->map(function ($p) {
            return [
                'price' => $p->price,
                'discount' => $p->discount,
                'inventory' => $p->inventory,
                'attribute_values' => $p->attributeValues->pluck('id')->toArray(),
            ];
        })->toArray();

        $oldPrices = old('prices', !empty($existingPrices) ? $existingPrices : [
            ['price' => '', 'discount' => '', 'inventory' => '', 'attribute_values' => []]
        ]);

        return view('panel.products.edit', compact('product', 'categories', 'attributes', 'oldPrices'));
    }

    public function update(ProductRequest $request, Product $product){
        $this->productService->update($product, $request->validated());
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

    public function restore($id){
        $product = Product::withTrashed()->findOrFail($id);

        if(!$product->trashed()){
            return back()->withErrors(['product' => 'این محصول حذف نشده است']);
        }

        $product->restore();

        return back()->with('success', 'محصول مورد نظر با موفقیت بازیابی شد');
    }

    public function trashed(){
        $products = Product::onlyTrashed()->with('images', 'categories', 'prices.attributeValues')->latest()->paginate(8)->withQueryString();
        $categories = Category::forSelection('product');

        return view('panel.products.trashed', compact('products', 'categories'));
    }

    public function storeCompareList(Request $request){
        $validated = $request->validate(['product_id' => 'required|integer|exists:products,id']);

        $user = auth('api')->user();

        $productId = $validated['product_id'];

        return DB::transaction(function () use ($user, $productId) {
            $user->newQuery()->whereKey($user->getKey())->lockForUpdate()->firstOrFail();

            $alreadyAdded = CompareList::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

            if($alreadyAdded){
                return response()->json([
                    'error' => false,
                    'message' => 'این محصول قبلا به لیست اضافه شده است',
                    'data' => $alreadyAdded,
                ]);
            }

            $count = CompareList::where('user_id', $user->id)->count();

            if ($count >= 4) {
                return response()->json([
                    'error' => true,
                    'message' => 'حداکثر می‌توانید ۴ محصول را مقایسه کنید.',
                ], 422);
            }

            $compareItem = CompareList::create([
                'user_id' => $user->id,
                'product_id' => $productId,
            ]);

            return response()->json([
                'error' => false,
                'message' => 'محصول با موفقیت به لیست مقایسه اضافه شد.',
                'data' => $compareItem,
            ], 201);
        });
    }

    public function removeCompareList(Request $request){
        $validated = $request->validate(['product_id' => 'required|integer|exists:products,id']);

        $user = auth('api')->user();
        CompareList::where('user_id', $user->id)
            ->where('product_id', $validated['product_id'])
            ->delete();

        return response()->json(['message' => 'محصول از لیست مقایسه حذف شد.']);
    }

    public function showCompareList(){

        $user = auth('api')->user();

        $compareList = CompareList::where('user_id', $user->id)
            ->with(['product.categories', 'product.images', 'product.prices.attributeValues.attribute'])
            ->get();

        return view('compare-list.show', compact('compareList'));
    }

}
