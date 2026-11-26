<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Price;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request){
        $query = Order::query()->with(['user', 'items'])->latest();

        if($request->filled('status')){
            $query->where('status', $request->status);
        }

        if($request->filled('payment_status')){
            $query->where('payment_status', $request->payment_status);
        }

        if($request->filled('search')){
            $search = trim($request->search);
            $query->where(function ($query) use ($search) {
                $query->where('tracking_code', 'like', "%{$search}%")
                    ->orWhere('recipient_name', 'like', "%{$search}%")
                    ->orWhere('recipient_phone', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        $pendingCount = Order::where('status', 'pending')->count();

        return view('panel.orders.index', compact('orders', 'pendingCount'));

    }

    public function show($id){
        $order = Order::findOrFail($id);

        $order->load(['user', 'items.product.images', 'items.price.attributeValues.attribute']);

        return view('panel.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id){
        $order = Order::findOrFail($id);

        $oldStatus = $order->status;
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'required|in:unpaid,paid,refunded'
        ]);

        try{
            DB::transaction(function () use ($request, $order, $oldStatus) {
                if ($oldStatus !== 'cancelled' && $request->status === 'cancelled'){
                    foreach($order->items as $item){
                        if($item->price_id){
                            Price::where('id', $item->price_id)->increment('inventory', $item->quantity);
                        }  
                    }       
                }

                if($oldStatus === 'cancelled' && $request->status !== 'cancelled'){
                    foreach($order->items as $item){
                        $affected = Price::where('id', $item->price_id)->where('inventory', '>=', $item->quantity)->decrement('inventory', $item->quantity);

                        if(! $affected){
                            throw new \Exception("امکان فعال‌سازی مجدد وجود ندارد؛ موجودی انبار برای کالای «{$item->product_name}» کافی نیست.");
                        }
                    }
                }

                $order->update([
                    'status' => $request->status,
                    'payment_status' => $request->payment_status,
                ]);
            });

            return redirect()->back()->with('success', 'وضعیت سفارش با موفقیت بروزرسانی شد.');
        } catch(\Exception $e){
            return redirect()->back()->withErrors(['status' => $e->getMessage()]);
        }
    }

    public function myOrders(){
        $user = auth('api')->user();

        if(! $user){
            return redirect()->route('login');
        }

        $orders = Order::where('user_id', $user->id)->with(['user', 'items.product.images', 'items.price.attributeValues.attribute'])->latest()->paginate(10)->withQueryString();

        return view('orders.myOrders', compact('orders'));
    }
}
