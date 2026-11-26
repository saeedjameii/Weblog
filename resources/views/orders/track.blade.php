@extends('layout.master')

@section('title', 'پیگیری آنلاین وضعیت سفارش — چی بهتره؟')

@section('content')
<main class="track-page">
    <div class="container track-container">

        {{-- کادر جستجو و استعلام سفارش --}}
        <section class="track-search-card glass">
            <div class="track-search-header">
                <div class="track-search-icon">
                    <x-icon name="box" />
                </div>
                <div>
                    <h1 class="track-search-title">پیگیری وضعیت سفارش</h1>
                    <p class="track-search-desc">
                        با وارد کردن کد پیگیری سفارش و شماره موبایل ثبت شده در هنگام خرید، از آخرین وضعیت پردازش و ارسال مرسوله خود مطلع شوید.
                    </p>
                </div>
            </div>

            <form action="{{ route('orders.track') }}" method="GET" class="track-form">
                <div class="track-form-grid">
                    <div class="track-input-group">
                        <label for="tracking_code" class="track-label">کد رهگیری سفارش <span class="track-req">*</span></label>
                        <input
                            type="text"
                            id="tracking_code"
                            name="tracking_code"
                            class="track-input @error('tracking_code') is-invalid @enderror"
                            placeholder="مثال: TRK-12345678"
                            value="{{ request('tracking_code', old('tracking_code')) }}"
                            dir="ltr"
                            required
                        >
                        @error('tracking_code')
                            <span class="track-error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="track-input-group">
                        <label for="phone" class="track-label">شماره موبایل تحویل‌گیرنده <span class="track-req">*</span></label>
                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            class="track-input @error('phone') is-invalid @enderror"
                            placeholder="مثال: 09123456789"
                            value="{{ request('phone', old('phone')) }}"
                            dir="ltr"
                            maxlength="11"
                            required
                        >
                        @error('phone')
                            <span class="track-error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="track-submit-col">
                        <button type="submit" class="btn-glow track-submit-btn">
                            <x-icon name="search" />
                            <span>استعلام وضعیت</span>
                        </button>
                    </div>
                </div>
            </form>

            {{-- پیام خطای عمومی در صورت یافت نشدن --}}
            @if ($errors->has('tracking'))
                <div class="track-alert track-alert--error" role="alert">
                    <x-icon name="close" />
                    <span>{{ $errors->first('tracking') }}</span>
                </div>
            @endif
        </section>

        {{-- نتایج رهگیری سفارش --}}
        @if ($order)
            @php
                $statusOrder = [
                    'pending'    => 1,
                    'processing' => 2,
                    'shipped'    => 3,
                    'delivered'  => 4,
                ];

                $currentStep = $statusOrder[$order->status] ?? 0;
                $isCancelled = $order->status === 'cancelled';

                $steps = [
                    1 => [
                        'key'   => 'pending',
                        'title' => 'ثبت سفارش',
                        'desc'  => 'سفارش در سیستم ثبت شد',
                    ],
                    2 => [
                        'key'   => 'processing',
                        'title' => 'آماده‌سازی و پردازش',
                        'desc'  => 'بسته‌بندی و آماده‌سازی در انبار',
                    ],
                    3 => [
                        'key'   => 'shipped',
                        'title' => 'تحویل به پست',
                        'desc'  => 'سفارش به پست تحویل شد',
                    ],
                    4 => [
                        'key'   => 'delivered',
                        'title' => 'تحویل به خریدار',
                        'desc'  => 'سفارش به مقصد رسید',
                    ],
                ];
            @endphp

            <div class="track-result-wrap">

                {{-- کارت هدر وضعیت سفارش --}}
                <div class="track-header-card glass">
                    <div class="track-header-meta">
                        <div class="track-header-title-row">
                            <span class="track-label-pill">کد سفارش:</span>
                            <strong class="track-header-code" dir="ltr">{{ $order->tracking_code }}</strong>
                        </div>
                        <div class="track-header-sub">
                            <span>تاریخ ثبت سفارش:</span>
                            <strong>{{ $order->created_at_jalali }}</strong>
                        </div>
                    </div>

                    <div class="track-badges-wrap">
                        {{-- نشان وضعیت کلی سفارش --}}
                        @if ($order->status === 'pending')
                            <span class="track-badge badge-pending">در انتظار بررسی</span>
                        @elseif ($order->status === 'processing')
                            <span class="track-badge badge-processing">در حال پردازش</span>
                        @elseif ($order->status === 'shipped')
                            <span class="track-badge badge-shipped">ارسال شده</span>
                        @elseif ($order->status === 'delivered')
                            <span class="track-badge badge-delivered">تحویل شده</span>
                        @elseif ($order->status === 'cancelled')
                            <span class="track-badge badge-cancelled">لغو شده</span>
                        @endif

                        {{-- نشان وضعیت پرداخت --}}
                        @if ($order->payment_status === 'paid')
                            <span class="track-badge badge-paid">پرداخت شده</span>
                        @elseif ($order->payment_status === 'unpaid')
                            <span class="track-badge badge-unpaid">در انتظار پرداخت</span>
                        @elseif ($order->payment_status === 'refunded')
                            <span class="track-badge badge-refunded">مسترد شده</span>
                        @endif
                    </div>
                </div>

                {{-- خط زمان و تایم‌لاین مراحل پیشرفت سفارش --}}
                @if ($isCancelled)
                    <div class="track-cancelled-banner">
                        <div class="track-cancelled-icon">
                            <x-icon name="close" />
                        </div>
                        <div class="track-cancelled-info">
                            <strong>این سفارش لغو گردیده است</strong>
                            <p>سفارش مورد نظر بنا به درخواست یا به علت عدم تکمیل فرآیند لغو شده و اقلام آن آزاد شده‌اند. در صورت وجود هرگونه مغایرت، با پشتیبانی تماس حاصل فرمایید.</p>
                        </div>
                    </div>
                @else
                    <div class="track-stepper-card glass">
                        <h2 class="track-stepper-heading">مراحل ارسال سفارش</h2>
                        <div class="track-stepper">
                            @foreach ($steps as $stepNum => $stepData)
                                @php
                                    $stepState = 'is-waiting';
                                    if ($currentStep > $stepNum) {
                                        $stepState = 'is-complete';
                                    } elseif ($currentStep === $stepNum) {
                                        $stepState = 'is-current';
                                    }
                                @endphp
                                <div class="track-step-node {{ $stepState }}">
                                    <div class="track-step-marker">
                                        @if ($stepState === 'is-complete')
                                            <x-icon name="check" />
                                        @else
                                            <span>{{ $stepNum }}</span>
                                        @endif
                                    </div>
                                    <div class="track-step-content">
                                        <h3 class="track-step-title">{{ $stepData['title'] }}</h3>
                                        <p class="track-step-desc">{{ $stepData['desc'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- گرید جزئیات: اقلام سفارش + مشخصات تحویل و مالی --}}
                <div class="track-details-grid">

                    {{-- ستون راست: لیست اقلام سفارش --}}
                    <div class="track-items-card glass">
                        <h2 class="track-section-title">
                            <x-icon name="cart" />
                            <span>اقلام سفارش ({{ $order->items->count() }} قلم)</span>
                        </h2>

                        <div class="track-items-list">
                            @foreach ($order->items as $item)
                                <div class="track-item-row">
                                    <div class="track-item-thumb">
                                        @if ($item->product && $item->product->images->isNotEmpty())
                                            <img src="{{ Storage::url($item->product->images->first()->path) }}" alt="{{ $item->product_name }}">
                                        @else
                                            <div class="track-item-no-thumb">
                                                <x-icon name="image" />
                                            </div>
                                        @endif
                                    </div>

                                    <div class="track-item-info">
                                        <h4 class="track-item-name">{{ $item->product_name }}</h4>
                                        @if ($item->variant_name)
                                            <span class="track-item-variant">{{ $item->variant_name }}</span>
                                        @endif
                                        <div class="track-item-pricing">
                                            <span>{{ $item->quantity }} عدد</span>
                                            <span>×</span>
                                            <span>{{ number_format((float) $item->unit_price) }} تومان</span>
                                        </div>
                                    </div>

                                    <div class="track-item-total">
                                        <span>{{ number_format((float) $item->total_price) }}</span>
                                        <small>تومان</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ستون چپ: مشخصات گیرنده و فاکتور مالی --}}
                    <div class="track-sidebar-col">

                        {{-- کارت تحویل‌گیرنده --}}
                        <div class="track-info-card glass">
                            <h3 class="track-card-title">
                                <x-icon name="user" />
                                <span>مشخصات تحویل‌گیرنده</span>
                            </h3>

                            <div class="track-info-list">
                                <div class="track-info-row">
                                    <span class="track-info-label">نام و نام خانوادگی:</span>
                                    <strong class="track-info-value">{{ $order->recipient_name }}</strong>
                                </div>
                                <div class="track-info-row">
                                    <span class="track-info-label">شماره تماس:</span>
                                    <strong class="track-info-value" dir="ltr">{{ $order->recipient_phone }}</strong>
                                </div>
                                <div class="track-info-row">
                                    <span class="track-info-label">مقصد:</span>
                                    <span class="track-info-value">{{ $order->province }}، {{ $order->city }}</span>
                                </div>
                                <div class="track-info-row">
                                    <span class="track-info-label">نشانی پستی:</span>
                                    <span class="track-info-value">{{ $order->address }}</span>
                                </div>
                                <div class="track-info-row">
                                    <span class="track-info-label">کد پستی:</span>
                                    <strong class="track-info-value" dir="ltr">{{ $order->postal_code }}</strong>
                                </div>
                                @if ($order->note)
                                    <div class="track-info-row track-info-note">
                                        <span class="track-info-label">یادداشت خریدار:</span>
                                        <p class="track-info-value">{{ $order->note }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- خلاصه مالی فاکتور --}}
                        <div class="track-info-card glass">
                            <h3 class="track-card-title">
                                <x-icon name="card" />
                                <span>خلاصه پرداخت</span>
                            </h3>

                            <div class="track-finance-list">
                                <div class="track-finance-row">
                                    <span>مجموع قیمت اقلام:</span>
                                    <strong>{{ number_format((float) $order->items->sum('total_price')) }} تومان</strong>
                                </div>

                                @if ($order->discount_amount > 0)
                                    <div class="track-finance-row track-discount-row">
                                        <span>تخفیف ({{ $order->discount_code }}):</span>
                                        <strong>- {{ number_format((float) $order->discount_amount) }} تومان</strong>
                                    </div>
                                @endif

                                <div class="track-finance-row">
                                    <span>هزینه بسته‌بندی و ارسال:</span>
                                    <span>{{ $order->shipping_cost > 0 ? number_format((float) $order->shipping_cost) . ' تومان' : 'رایگان' }}</span>
                                </div>

                                <div class="track-finance-row track-total-row">
                                    <span>مبلغ پرداختی:</span>
                                    <strong class="track-total-val">{{ number_format((float) $order->total_amount) }} تومان</strong>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        @endif

    </div>
</main>
@endsection
