@extends('layout.master')

@section('title', 'سفارش شما با موفقیت ثبت شد — چی بهتره؟')

@section('content')
<main class="checkout-success-page">
    <div class="container article-wrap">
        {{-- پیام موفقیت اصلی --}}
        <div class="success-hero-card glass">
            <div class="success-icon-badge">
                <x-icon name="check" />
            </div>

            <h1>سفارش شما با موفقیت ثبت شد!</h1>
            <p>
                از خرید شما سپاسگزاریم. سفارش شما جهت پردازش و آماده‌سازی به واحد انبار ارسال شد.
            </p>

            {{-- کادر کد رهگیری --}}
            <div class="tracking-pill">
                <span>کد رهگیری و پیگیری سفارش:</span>
                <strong dir="ltr">{{ $order->tracking_code }}</strong>
            </div>
        </div>

        {{-- فاکتور و مشخصات تحویل --}}
        <div class="invoice-card glass">
            <h2>مشخصات تحویل و فاکتور</h2>

            <div class="invoice-meta-grid">
                <div>
                    <span class="invoice-meta-label">تحویل‌گیرنده:</span>
                    <strong class="invoice-meta-val">{{ $order->recipient_name }}</strong>
                </div>
                <div>
                    <span class="invoice-meta-label">شماره تماس:</span>
                    <strong class="invoice-meta-val" dir="ltr">{{ $order->recipient_phone }}</strong>
                </div>
                <div class="full-span">
                    <span class="invoice-meta-label">آدرس پستی:</span>
                    <strong class="invoice-meta-val">
                        {{ $order->province }}، {{ $order->city }}، {{ $order->address }}
                        <small class="invoice-meta-label">(کد پستی: {{ $order->postal_code }})</small>
                    </strong>
                </div>
                @if ($order->note)
                    <div class="full-span">
                        <span class="invoice-meta-label">یادداشت سفارش:</span>
                        <p class="invoice-meta-val">{{ $order->note }}</p>
                    </div>
                @endif
            </div>

            {{-- اقلام خریداری‌شده --}}
            <h3 class="invoice-items-title">اقلام سفارش</h3>
            <div class="invoice-table-wrap">
                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th>محصول</th>
                            <th>تعداد</th>
                            <th>قیمت واحد</th>
                            <th class="col-total">مجموع</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->product_name }}</strong>
                                    @if ($item->variant_name)
                                        <div class="invoice-table-variant">
                                            {{ $item->variant_name }}
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format((float) $item->unit_price) }} ت</td>
                                <td class="col-total">
                                    {{ number_format((float) $item->total_price) }} ت
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- خلاصه مالی فاکتور --}}
            <div class="summary-list summary-list--bordered">
                <div class="summary-row">
                    <span>جمع اقلام:</span>
                    <strong>{{ number_format((float) $order->items->sum('total_price')) }} تومان</strong>
                </div>

                @if ($order->discount_amount > 0)
                    <div class="summary-row summary-row--discount">
                        <span>تخفیف ({{ $order->discount_code }}):</span>
                        <strong>- {{ number_format((float) $order->discount_amount) }} تومان</strong>
                    </div>
                @endif

                <div class="summary-row">
                    <span>هزینه ارسال:</span>
                    <span>{{ $order->shipping_cost > 0 ? number_format((float) $order->shipping_cost) . ' تومان' : 'رایگان' }}</span>
                </div>

                <div class="summary-row summary-row--total summary-row--total-bordered">
                    <span>مبلغ نهایی پرداخت شده:</span>
                    <strong class="summary-total-amount">
                        {{ number_format((float) $order->total_amount) }} تومان
                    </strong>
                </div>
            </div>
        </div>

        {{-- دکمه‌های اقدام نهایی --}}
        <div class="success-actions-row">
            <a href="{{ route('products.index') }}" class="btn-glow">
                <x-icon name="shop" />
                <span>ادامه خرید از فروشگاه</span>
            </a>
            <a href="{{ route('home') }}" class="btn btn-secondary">
                صفحه اصلی
            </a>
        </div>
    </div>
</main>
@endsection
