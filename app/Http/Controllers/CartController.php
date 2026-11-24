<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequest;
use App\Models\Cart;
use App\Models\Price;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function getCart(){
        $sessionId = session()->getId();

        if(auth('api')->check()){
            $userId = auth('api')->id();

            $userCart = Cart::firstOrCreate(['user_id' => $userId]);

            $guestCart = Cart::where('session_id', $sessionId)->whereNull('user_id')->first();

            if($guestCart && $guestCart->id !== $userCart->id){
                foreach($guestCart->items as $guestItem){
                    $existingItem = $userCart->items()->where('price_id', $guestItem->price_id)->first();

                    if($existingItem){
                        $existingItem->increment('quantity', $guestItem->quantity);
                        $guestItem->delete();
                    } else{
                        $guestItem->update(['cart_id' => $userCart->id]);
                    }
                }

                $guestCart->delete();

            }

            return $userCart;
        }

        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    public function index(){
        $cart = $this->getCart()->load(['items.product', 'items.price.attributeValues.attribute']);

        $total = $cart->items->sum(function ($item){
            return ($item->price?->final_price ?? 0) * $item->quantity;
        });

        return view('cart.index', compact('cart', 'total'));
    }

    public function add(CartRequest $request){
        $cart = $this->getCart();
        $quantity = (int) $request->input('quantity', 1);

        $price = Price::findOrFail($request->price_id);

        $cartItem = $cart->items()->where('price_id', $request->price_id)->first();
        $currentQuantityInCart = $cartItem ? $cartItem->quantity : 0;
        $totalRequestedQuantity = $currentQuantityInCart + $quantity;

        if($totalRequestedQuantity > $price->inventory) {
            return redirect()->back()->withErrors([
            'quantity' => "موجودی انبار کافی نیست. حداکثر موجودی قابل سفارش: {$price->inventory} عدد (تعداد در سبد شما: {$currentQuantityInCart})"
            ]);
        }

        if($cartItem){
            $cartItem->increment('quantity', $quantity);
        } else{
            $cart->items()->create([
                'product_id' => $request->product_id,
                'price_id' => $request->price_id,
                'quantity'   => $quantity,
            ]);
        }

        return redirect()->back()->with('success', 'محصول با موفقیت به سبد خرید اضافه شد.');
    }

    public function update(Request $request, $itemId){
        $request->validate(['quantity' => 'required|integer|min:1']);

        $cart = $this->getCart();

        $cartItem = $cart->items()->with('price')->findOrFail($itemId);

        if (! $cartItem->price) {
            return redirect()->back()->withErrors([
                'quantity' => 'تنوع انتخابی برای این محصول دیگر در دسترس نیست.'
            ]);
        }

        if($request->quantity > $cartItem->price->inventory) {
            return redirect()->back()->withErrors([
            'quantity' => "تعداد درخواستی بیشتر از موجودی انبار است. حداکثر موجودی: {$cartItem->price->inventory} عدد"
            ]);
        }

        $cartItem->update([
            'quantity' => $request->quantity
        ]);

        return redirect()->back()->with('success', 'سبد خرید با موفقیت بروزرسانی شد');
    }

    public function remove($itemId){
        $cart = $this->getCart();

        $cartItem = $cart->items()->findOrFail($itemId);

        $cartItem->delete();

        return redirect()->back()->with('success', 'محصول با موفقیت از سبد خرید حذف شد');
    }
}
