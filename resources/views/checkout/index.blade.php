@extends('layout.master')

@section('title', 'تسویه‌حساب و ثبت سفارش — چی بهتره؟')

@section('content')
<main class="checkout-page">
    <div class="container">
        {{-- مسیر صفحه --}}
        <nav aria-label="مسیر صفحه">
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">صفحهٔ اصلی</a></li>
                <li><a href="{{ route('cart.index') }}">سبد خرید</a></li>
                <li aria-current="page">تسویه‌حساب و اطلاعات تحویل</li>
            </ol>
        </nav>

        {{-- پیام‌های خطا --}}
        @if ($errors->any())
            <div class="alert alert-danger glass">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- هدر صفحه تسویه‌حساب --}}
        <section class="checkout-heading glass">
            <span class="sx-kicker sx-kicker--shop">
                <x-icon name="card" /> گام نهایی خرید
            </span>
            <h1 class="sx-title">اطلاعات تحویل و ثبت سفارش</h1>
        </section>

        <form action="{{ route('checkout.process') }}" method="POST">
            @csrf

            <div class="checkout-layout">
                {{-- ستون راست: فرم اطلاعات آدرس و گیرنده --}}
                <div class="checkout-form-card glass">
                    <h2 class="checkout-section-title">
                        مشخصات تحویل‌گیرنده
                    </h2>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="recipient_name" class="form-label">نام و نام خانوادگی تحویل‌گیرنده <span class="req">*</span></label>
                            <input type="text" id="recipient_name" name="recipient_name" class="form-control"
                                   value="{{ old('recipient_name', $user->first_name . ' ' . $user->last_name) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="recipient_phone" class="form-label">شماره تماس (موبایل) <span class="req">*</span></label>
                            <input type="tel" id="recipient_phone" name="recipient_phone" class="form-control"
                                   placeholder="۰۹۱۲۳۴۵۶۷۸۹" value="{{ old('recipient_phone', $user->phone_number) }}" dir="ltr" required>
                        </div>
                    </div>

                    <h2 class="checkout-section-title checkout-section-title--space">
                        آدرس پستی
                    </h2>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="province" class="form-label">استان <span class="req">*</span></label>
                            <input type="text" id="province" name="province" class="form-control" placeholder="مثلاً تهران" value="{{ old('province') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="city" class="form-label">شهر <span class="req">*</span></label>
                            <input type="text" id="city" name="city" class="form-control" placeholder="مثلاً تهران" value="{{ old('city') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address" class="form-label">نشانی دقیق پستی <span class="req">*</span></label>
                        <textarea id="address" name="address" rows="3" class="form-control"
                                  placeholder="نام خیابان، کوچه، پلاک، طبقه و واحد" required>{{ old('address') }}</textarea>
                    </div>

                    <div class="form-group form-group--postal">
                        <label for="postal_code" class="form-label">کد پستی ۱۰ رقمی <span class="req">*</span></label>
                        <input type="text" id="postal_code" name="postal_code" class="form-control"
                               placeholder="۱۰ رقم بدون خط تیره" maxlength="10" dir="ltr" value="{{ old('postal_code') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="note" class="form-label">توضیحات یا یادداشت تحویل (اختیاری)</label>
                        <textarea id="note" name="note" rows="2" class="form-control"
                                  placeholder="اگر نکته‌ای در مورد زمان تحویل یا ارسال وجود دارد بنویسید...">{{ old('note') }}</textarea>
                    </div>

                    <div class="form-group form-group--discount">
                        <label for="discount_code" class="form-label">کد تخفیف دارید؟ (اختیاری)</label>
                        <div class="discount-input-wrap">
                            <input type="text" id="discount_code" name="discount_code" class="form-control"
                                   placeholder="کد تخفیف" value="{{ old('discount_code') }}" dir="ltr">
                        </div>
                    </div>
                </div>

                {{-- ستون چپ: پیش‌فاکتور و خلاصه خرید --}}
                <aside class="checkout-summary-card glass">
                    <h2>خلاصه اقلام سفارش</h2>

                    {{-- لیست کوتاه کالاها --}}
                    <div class="checkout-items-preview">
                        @foreach ($cart->items as $item)
                            <div class="checkout-preview-row">
                                <div class="checkout-preview-name">
                                    <strong>{{ $item->product?->name ?? 'کالای نامشخص' }}</strong>
                                    <small>{{ $item->quantity }} عدد</small>
                                </div>
                                <div class="checkout-preview-price">
                                    {{ number_format((float) (($item->price?->final_price ?? 0) * $item->quantity)) }} ت
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- مبالغ مالی --}}
                    <div class="summary-list">
                        <div class="summary-row">
                            <span>قیمت کل کالاها:</span>
                            <strong>{{ number_format((float) $subTotal) }} تومان</strong>
                        </div>

                        <div class="summary-row">
                            <span>هزینه ارسال:</span>
                            @if ($shippingCost == 0)
                                <span class="badge badge--ok">رایگان</span>
                            @else
                                <span>{{ number_format((float) $shippingCost) }} تومان</span>
                            @endif
                        </div>

                        <hr class="summary-divider">

                        <div class="summary-row summary-row--total">
                            <span>مبلغ نهایی:</span>
                            <div class="summary-total-amount">
                                {{ number_format((float) $totalAmount) }}
                                <small>تومان</small>
                            </div>
                        </div>
                    </div>

                    {{-- دکمه ثبت سفارش --}}
                    <div class="summary-actions">
                        <button type="submit" class="btn-glow">
                            <x-icon name="check" />
                            <span>ثبت قطعی سفارش و پرداخت</span>
                        </button>
                        <a href="{{ route('cart.index') }}" class="btn btn-secondary">
                            بازگشت به سبد خرید
                        </a>
                    </div>
                </aside>
            </div>
        </form>
    </div>
</main>
@endsection
