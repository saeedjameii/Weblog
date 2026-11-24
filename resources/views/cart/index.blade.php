@extends('layout.master')

@section('title', 'سبد خرید — چی بهتره؟')

@section('content')
<main class="cart-page">
    <div class="container">
        {{-- مسیر صفحه (Breadcrumb) --}}
        <nav aria-label="مسیر صفحه">
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">صفحهٔ اصلی</a></li>
                <li><a href="{{ route('products.index') }}">فروشگاه</a></li>
                <li aria-current="page">سبد خرید</li>
            </ol>
        </nav>

        {{-- پیام‌های موفقیت یا خطا --}}
        @if (session('success'))
            <div class="alert alert-success glass">
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger glass">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- هدر سبد خرید --}}
        <section class="cart-heading glass">
            <div>
                <span class="sx-kicker sx-kicker--shop"><x-icon name="cart" /> خریدهای شما</span>
                <h1 class="sx-title">سبد خرید من</h1>
            </div>
            @if ($cart->items->isNotEmpty())
                <span class="badge badge--shop">{{ $cart->items->sum('quantity') }} قلم کالا</span>
            @endif
        </section>

        @if ($cart->items->isEmpty())
            {{-- حالت سبد خرید خالی --}}
            <section class="cart-empty glass">
                <div class="cart-empty__icon">
                    <x-icon name="cart" />
                </div>
                <h2>سبد خرید شما در حال حاضر خالی است!</h2>
                <p>
                    می‌توانید برای مشاهده محصولات جدید، تخفیف‌ها و گزینه‌های متنوع به فروشگاه بروید و کالاهای مورد علاقه خود را انتخاب کنید.
                </p>
                <a class="btn-glow" href="{{ route('products.index') }}">
                    <x-icon name="shop" />
                    <span>مشاهده محصولات فروشگاه</span>
                </a>
            </section>
        @else
            {{-- چیدمان دو ستونه سبد خرید --}}
            <div class="cart-layout">
                {{-- ستون راست: لیست کالاهای سبد --}}
                <div class="cart-items-wrap">
                    @foreach ($cart->items as $item)
                        <article class="cart-item-card">
                            {{-- تصویر محصول --}}
                            <div class="cart-item-img">
                                <a href="{{ route('products.show', $item->product) }}">
                                    @if ($item->product->images->isNotEmpty())
                                        <img src="{{ Storage::url($item->product->images->first()->path) }}" alt="{{ $item->product->name }}">
                                    @else
                                        <div class="empty-box"><x-icon name="box" /></div>
                                    @endif
                                </a>
                            </div>

                            {{-- جزییات محصول --}}
                            <div class="cart-item-details">
                                <div>
                                    <div class="cart-item-head">
                                        <h2 class="cart-item-title">
                                            <a href="{{ route('products.show', $item->product) }}">
                                                {{ $item->product->name }}
                                            </a>
                                        </h2>

                                        {{-- دکمه حذف --}}
                                        <form action="{{ route('cart.remove', $item->id) }}" method="POST" onsubmit="return confirm('آیا از حذف این کالا از سبد خرید مطمئن هستید؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="cart-item-btn-del" title="حذف این کالا" aria-label="حذف">
                                                <x-icon name="trash" />
                                            </button>
                                        </form>
                                    </div>

                                    {{-- مشخصات و تنوع انتخابی --}}
                                    @if ($item->price && $item->price->attributeValues->isNotEmpty())
                                        <div class="cart-item-variants">
                                            @foreach ($item->price->attributeValues as $val)
                                                <span class="badge">
                                                    {{ $val->attribute?->name ?? 'ویژگی' }}: <strong>{{ $val->value }}</strong>
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                {{-- بخش پایینی: کنترل تعداد و قیمت --}}
                                <div class="cart-item-footer">
                                    {{-- فرم ویرایش تعداد --}}
                                    <form action="{{ route('cart.update', $item->id) }}" method="POST" class="cart-qty-form">
                                        @csrf
                                        @method('PUT')
                                        <label for="qty_{{ $item->id }}">تعداد:</label>
                                        <input type="number" id="qty_{{ $item->id }}" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->price?->inventory ?? 1 }}" class="cart-qty-input">
                                        <button type="submit" class="btn btn-secondary btn-sm cart-qty-btn" title="بروزرسانی تعداد">
                                            ثبت
                                        </button>
                                        @if ($item->price && $item->price->inventory < 5)
                                            <small class="cart-low-stock">(تنها {{ $item->price->inventory }} عدد در انبار)</small>
                                        @elseif (! $item->price)
                                            <small class="cart-low-stock cart-out-of-stock">(تنوع دیگر در دسترس نیست)</small>
                                        @endif
                                    </form>

                                    {{-- قیمت کل این ردیف --}}
                                    <div class="cart-item-price">
                                        @if ($item->price && $item->price->discount > 0)
                                            <div class="cart-item-old-price">
                                                {{ number_format((float) ($item->price->price * $item->quantity)) }}
                                            </div>
                                        @endif
                                        <div class="cart-item-final-price">
                                            {{ number_format((float) (($item->price?->final_price ?? 0) * $item->quantity)) }}
                                            <small>تومان</small>
                                        </div>
                                    </div>
                                </div>
                            </article>
                    @endforeach
                </div>

                {{-- ستون چپ: فاکتور و خلاصه سفارش --}}
                <aside class="cart-summary glass">
                    <h2>خلاصه سفارش</h2>

                    <div class="summary-list">
                        <div class="summary-row">
                            <span>تعداد کالاها:</span>
                            <strong>{{ $cart->items->sum('quantity') }} عدد</strong>
                        </div>

                        @php
                            $rawSubtotal = $cart->items->sum(fn($i) => ($i->price?->price ?? 0) * $i->quantity);
                            $totalDiscount = $rawSubtotal - $total;
                        @endphp

                        @if ($totalDiscount > 0)
                            <div class="summary-row">
                                <span>قیمت کل بدون تخفیف:</span>
                                <span>{{ number_format((float) $rawSubtotal) }} تومان</span>
                            </div>
                            <div class="summary-row summary-row--discount">
                                <span>سود شما از خرید:</span>
                                <strong>{{ number_format((float) $totalDiscount) }} تومان</strong>
                            </div>
                        @endif

                        <div class="summary-row summary-row--hint">
                            <span>هزینه ارسال:</span>
                            <span>محاسبه در مرحله بعد</span>
                        </div>

                        <hr class="summary-divider">

                        <div class="summary-row summary-row--total">
                            <span>مبلغ قابل پرداخت:</span>
                            <div class="summary-total-amount">
                                {{ number_format((float) $total) }}
                                <small>تومان</small>
                            </div>
                        </div>
                    </div>

                    {{-- دکمه‌های اقدام نهایی --}}
                    <div class="summary-actions">
                        <a href="{{ route('checkout.index') }}" class="btn-glow">
                            تکمیل سفارش و تسویه‌حساب
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">
                            بازگشت به فروشگاه و ادامه خرید
                        </a>
                    </div>

                    {{-- نشان‌های اطمینان --}}
                    <div class="summary-trust">
                        <div class="summary-trust-item">
                            <x-icon name="shield" />
                            <span>ضمانت اصالت و سلامت کالا</span>
                        </div>
                        <div class="summary-trust-item">
                            <x-icon name="check" />
                            <span>امکان بازگشت کالا تا ۷ روز کاری</span>
                        </div>
                    </div>
                </aside>
            </div>
        @endif
    </div>
</main>
@endsection
