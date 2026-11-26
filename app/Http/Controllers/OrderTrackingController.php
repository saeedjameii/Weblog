<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function index(Request $request){
        $order = null;

        if($request->filled('tracking_code') || $request->filled('phone')) {
            $validated = $request->validate([
                'tracking_code' => 'required|string',
                'phone' => 'required|size:11|regex:/^09[0-9]{9}$/',
            ]);

            $order = Order::where('tracking_code', trim($request->tracking_code))->where('recipient_phone', trim($request->phone))->with(['items.product.images'])->first();

            if(! $order){
                return redirect()->back()->withInput()->withErrors(['tracking' => 'سفارشی با این کد رهگیری یا شماره تماس یافت نشد.']);
            }

        }

        return view('orders.track', compact('order'));
    }
}
