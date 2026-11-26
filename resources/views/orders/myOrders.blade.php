@extends('layout.master')

@section('title', 'سفارش‌های من — چی بهتره؟')

@section('content')
<main class="my-orders-page">
    <div class="container my-orders-container">

        {{-- سربرگ صفحه --}}
        <div class="my-orders-header glass">
            <div class="my-orders-header-info">
                <div class="my-orders-header-icon">
                    <x-icon name="box" />
                </div>
                <div>
                    <h1 class="my-orders-title">سفارش‌های من</h1>
                    <p class="my-orders-desc">
                        تاریخچه خریدها، وضعیت پردازش و مشخصات مرسوله‌های ثبت‌شده در حساب کاربری شما
                    </p>
                </div>
            </div>

            <div class="my-orders-header-action">
                <a href="{{ route('orders.track') }}" class="btn btn-secondary btn-sm">
                    <x-icon name="search" />
                    <span>پیگیری با کد رهگیری</span>
                </a>
            </div>
        </div>

        {{-- حالت عدم وجود سفارش --}}
        @if ($orders->isEmpty())
            <div class="my-orders-empty-card glass">
                <div class="my-orders-empty-icon">
                    <x-icon name="cart" />
                </div>
                <h2>هنوز هیچ سفارشی ثبت نکرده‌اید!</h2>
                <p>می‌توانید از بخش فروشگاه، جدیدترین محصولات را بررسی کرده و سفارش خود را ثبت نمایید.</p>
                <a href="{{ route('products.index') }}" class="btn-glow">
                    <x-icon name="shop" />
                    <span>مشاهده محصولات فروشگاه</span>
                </a>
            </div>
        @else
            {{-- لیست سفارش‌ها --}}
            <div class="my-orders-list">
                @foreach ($orders as $order)
                    <article class="my-order-card glass">
                        {{-- نوار بالای هر کارت سفارش --}}
                        <div class="my-order-card-header">
                            <div class="my-order-meta-group">
                                <div class="my-order-code-row">
                                    <span class="my-order-label">کد سفارش:</span>
                                    <strong class="my-order-code" dir="ltr">{{ $order->tracking_code }}</strong>
                                </div>
                                <div class="my-order-date-row">
                                    <span class="my-order-label">تاریخ ثبت:</span>
                                    <span>{{ $order->created_at_jalali }}</span>
                                </div>
                            </div>

                            <div class="my-order-badges">
                                {{-- وضعیت سفارش --}}
                                @if ($order->status === 'pending')
                                    <span class="track-badge badge-pending">در انتظار بررسی</span>
                                @elseif ($order->status === 'processing')
                                    <span class="track-badge badge-processing">در حال پردازش</span>
                                @elseif ($order->status === 'shipped')
                                    <span class="track-badge badge-shipped">ارسال شده</span>
                                @elseif ($order->status === 'delivered')
                                    <span class="track-badge badge-delivered">تحویل داده شد</span>
                                @elseif ($order->status === 'cancelled')
                                    <span class="track-badge badge-cancelled">لغو شده</span>
                                @endif

                                {{-- وضعیت پرداخت --}}
                                @if ($order->payment_status === 'paid')
                                    <span class="track-badge badge-paid">پرداخت شده</span>
                                @elseif ($order->payment_status === 'unpaid')
                                    <span class="track-badge badge-unpaid">در انتظار پرداخت</span>
                                @elseif ($order->payment_status === 'refunded')
                                    <span class="track-badge badge-refunded">مسترد شده</span>
                                @endif
                            </div>
                        </div>

                        {{-- لیست اقلام این سفارش --}}
                        <div class="my-order-items-wrap">
                            @foreach ($order->items as $item)
                                <div class="my-order-item-row">
                                    <div class="my-order-item-thumb">
                                        @if ($item->product && $item->product->images->isNotEmpty())
                                            <img src="{{ Storage::url($item->product->images->first()->path) }}" alt="{{ $item->product_name }}">
                                        @else
                                            <div class="my-order-item-no-thumb">
                                                <x-icon name="image" />
                                            </div>
                                        @endif
                                    </div>

                                    <div class="my-order-item-details">
                                        <h3 class="my-order-item-name">{{ $item->product_name }}</h3>

                                        <div class="my-order-item-features">
                                            {{-- تفکیک رنگ: فقط در صورتی که رنگ دارد نمایش داده می‌شود، در غیر این صورت اصلاً هیچی نمایش نمی‌دهد --}}
                                            @if ($item->color_name)
                                                <span class="order-color-pill">
                                                    <span class="order-color-label">رنگ:</span>
                                                    <strong class="order-color-value">{{ $item->color_name }}</strong>
                                                </span>
                                            @endif

                                            {{-- سایر ویژگی‌ها بدون تکرار رنگ --}}
                                            @if ($item->other_variants)
                                                <span class="order-other-specs">
                                                    {{ $item->other_variants }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="my-order-item-pricing-row">
                                            <span>تعداد: <strong>{{ $item->quantity }}</strong></span>
                                            <span>•</span>
                                            <span>قیمت واحد: <strong>{{ number_format((float) $item->unit_price) }}</strong> تومان</span>
                                        </div>
                                    </div>

                                    <div class="my-order-item-total">
                                        <span>{{ number_format((float) $item->total_price) }}</span>
                                        <small>تومان</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- فوتر کارت سفارش و دکمه پیگیری مستقیم --}}
                        <div class="my-order-card-footer">
                            <div class="my-order-footer-total">
                                <span>مبلغ کل سفارش:</span>
                                <strong class="my-order-total-amount">{{ number_format((float) $order->total_amount) }} تومان</strong>
                            </div>

                            <div class="my-order-footer-actions">
                                <a href="{{ route('orders.track', ['tracking_code' => $order->tracking_code, 'phone' => $order->recipient_phone]) }}" class="btn btn-secondary btn-sm">
                                    <x-icon name="box" />
                                    <span>مشاهده فاکتور و رهگیری مرحله‌به‌مرحله</span>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- صفحه‌بندی در صورت فعال بودن پجینیشن --}}
            @if (method_exists($orders, 'links'))
                <div class="my-orders-pagination">
                    {{ $orders->links() }}
                </div>
            @endif
        @endif

    </div>
</main>
@endsection
