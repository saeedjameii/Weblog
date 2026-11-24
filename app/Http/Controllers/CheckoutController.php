<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index(){
        $user = auth('api')->user();

        if(! $user){
            return redirect()->route('login')->withErrors([
                'email' => 'برای تشکیل فرآیند خرید، لطفا ابتدا وارد حساب کاربری خود شوید.'
            ]);
        }

        $cart = Cart::where('user_id', $user->id)->with(['items.product', 'items.price.attributeValues.attribute'])->first();

        if(!$cart || $cart->items->isEmpty()){
            return redirect()->route('cart.index')->withErrors(['cart' => 'سبد خرید شما خالی است! لطفاً ابتدا کالایی را انتخاب کنید.']);
        }

        foreach($cart->items as $item){
            if($item->quantity > $item->price->inventory){
                return redirect()->route('cart.index')->withErrors([
                    'cart' => "موجودی کالای «{$item->product->name}» در انبار تغییر کرده است (حداکثر موجودی فعلی: {$item->price->inventory} عدد)."
                ]);
            }
        }

        $subTotal = $cart->items->sum(function ($item){
            return $item->price->final_price * $item->quantity;
        });

        $discountAmount = 0;
        $shippingCost = $subTotal >= 10000000 ? 0 : 150000;
        $totalAmount = max(0, ($subTotal - $discountAmount) + $shippingCost);

        return view('checkout.index', compact('cart', 'user', 'subTotal', 'shippingCost', 'totalAmount'));
    }

    public function process(CheckoutRequest $request)
    {
        $user = auth('api')->user();

        $cart = Cart::where('user_id', $user->id)
            ->with(['items.product', 'items.price.attributeValues.attribute'])
            ->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->withErrors([
                'cart' => 'سبد خرید شما خالی است.'
            ]);
        }
        foreach ($cart->items as $item) {
            if ($item->quantity > $item->price->inventory) {
                return redirect()->route('cart.index')->withErrors([
                    'cart' => "متاسفانه موجودی «{$item->product->name}» حین ثبت سفارش به اتمام رسید یا تغییر کرد."
                ]);
            }
        }

        return DB::transaction(function () use ($request, $user, $cart) {
            $subTotal = $cart->items->sum(function ($item) {
                return $item->price->final_price * $item->quantity;
            });

            $shippingCost = $subTotal >= 10000000 ? 0 : 150000;
            $discountCode = $request->input('discount_code');
            $discountAmount = 0;
            $totalAmount = max(0, ($subTotal - $discountAmount) + $shippingCost);

            $trackingCode = 'ORD-' . date('ymd') . '-' . strtoupper(Str::random(4));

            $order = Order::create([
                'user_id'         => $user->id,
                'tracking_code'   => $trackingCode,
                'status'          => 'pending',
                'payment_status'  => 'unpaid',
                'payment_method'  => 'online',
                'discount_code'   => $discountCode,
                'discount_amount' => $discountAmount,
                'total_amount'    => $totalAmount,
                'shipping_cost'   => $shippingCost,
                'recipient_name'  => $request->recipient_name,
                'recipient_phone' => $request->recipient_phone,
                'province'        => $request->province,
                'city'            => $request->city,
                'address'         => $request->address,
                'postal_code'     => $request->postal_code,
                'note'            => $request->note,
            ]);

            foreach ($cart->items as $item) {
                $variantDetails = $item->price->attributeValues->map(function ($val) {
                    return ($val->attribute?->name ?? 'ویژگی') . ': ' . $val->value;
                })->implode(' | ');

                $order->items()->create([
                    'product_id'   => $item->product_id,
                    'price_id'     => $item->price_id,
                    'product_name' => $item->product->name,
                    'variant_name' => $variantDetails ?: null,
                    'unit_price'   => $item->price->final_price,
                    'quantity'     => $item->quantity,
                    'total_price'  => $item->price->final_price * $item->quantity,
                ]);

                $item->price->decrement('inventory', $item->quantity);
            }

            $cart->items()->delete();

            return redirect()->route('checkout.success', $order->tracking_code)->with('success', 'سفارش شما با موفقیت ثبت شد.');
        });
    }

    public function success($trackingCode)
    {
        $user = auth('api')->user();

        $order = Order::where('tracking_code', $trackingCode)->where('user_id', $user->id)->with(['items.product', 'items.price'])->firstOrFail();

        return view('checkout.success', compact('order'));
    }
}