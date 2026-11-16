@extends('layout.master')

@section('title', 'سبد خرید — چی بهتره؟')

@section('content')
<main class="cart-page" style="padding-block: 2rem 4rem;">
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
            <div class="alert alert--ok glass" style="margin-block: 1rem 1.5rem; padding: 1rem 1.25rem; border-radius: var(--radius-md); background: rgba(41, 93, 67, 0.1); border: 1px solid var(--forest, #295d43); color: var(--forest-dark, #1d4934); display: flex; align-items: center; justify-content: space-between;">
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert--err glass" style="margin-block: 1rem 1.5rem; padding: 1rem 1.25rem; border-radius: var(--radius-md); background: rgba(181, 71, 58, 0.1); border: 1px solid var(--danger, #b5473a); color: var(--danger, #b5473a);">
                <ul style="margin: 0; padding-inline-start: 1.2rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- هدر سبد خرید --}}
        <section class="cart-heading glass" style="padding: 1.5rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <span class="sx-kicker sx-kicker--shop" style="display: inline-flex; align-items: center; gap: 6px;"><x-icon name="cart" /> خریدهای شما</span>
                <h1 class="sx-title" style="margin: 0.25rem 0 0; font-size: 1.5rem;">سبد خرید من</h1>
            </div>
            @if ($cart->items->isNotEmpty())
                <span class="badge badge--shop" style="font-size: 0.95rem; padding: 6px 14px;">{{ $cart->items->sum('quantity') }} قلم کالا</span>
            @endif
        </section>

        @if ($cart->items->isEmpty())
            {{-- حالت سبد خرید خالی --}}
            <section class="cart-empty glass" style="text-align: center; padding: 4rem 2rem; border-radius: var(--radius-md); background: rgba(255, 255, 255, 0.85); border: 1px solid var(--t-line);">
                <div style="font-size: 3.5rem; color: var(--t-text-soft); margin-bottom: 1rem;">
                    <x-icon name="cart" style="width: 64px; height: 64px; margin: auto;" />
                </div>
                <h2 style="font-size: 1.4rem; color: var(--ink); margin-bottom: 0.5rem;">سبد خرید شما در حال حاضر خالی است!</h2>
                <p style="color: var(--t-text-soft); margin-bottom: 1.5rem; max-width: 480px; margin-inline: auto;">
                    می‌توانید برای مشاهده محصولات جدید، تخفیف‌ها و گزینه‌های متنوع به فروشگاه بروید و کالاهای مورد علاقه خود را انتخاب کنید.
                </p>
                <a class="btn-glow" href="{{ route('products.index') }}" style="display: inline-flex; align-items: center; gap: 8px;">
                    <x-icon name="shop" />
                    <span>مشاهده محصولات فروشگاه</span>
                </a>
            </section>
        @else
            {{-- چیدمان دو ستونه سبد خرید --}}
            <div class="cart-layout" style="display: grid; grid-template-columns: minmax(0, 1fr); gap: 1.5rem; align-items: start;">
                <style>
                    @media (min-width: 992px) {
                        .cart-layout {
                            grid-template-columns: minmax(0, 1fr) 340px !important;
                        }
                    }
                    .cart-item-card {
                        display: grid;
                        grid-template-columns: 88px minmax(0, 1fr);
                        gap: 1.25rem;
                        padding: 1.25rem;
                        background: rgba(255, 255, 255, 0.88);
                        border: 1px solid var(--t-border, #dce4d7);
                        border-radius: var(--radius-md, 14px);
                        margin-bottom: 1rem;
                        transition: box-shadow 0.2s ease;
                    }
                    .cart-item-card:hover {
                        box-shadow: var(--shadow-sm, 0 4px 14px rgba(0,0,0,0.05));
                    }
                    @media (max-width: 600px) {
                        .cart-item-card {
                            grid-template-columns: 72px minmax(0, 1fr);
                            gap: 0.75rem;
                            padding: 0.85rem;
                        }
                        .cart-item-footer {
                            flex-direction: column;
                            align-items: flex-start !important;
                            gap: 0.75rem;
                        }
                    }
                </style>

                {{-- ستون راست: لیست کالاهای سبد --}}
                <div class="cart-items-wrap">
                    @foreach ($cart->items as $item)
                        <article class="cart-item-card">
                            {{-- تصویر محصول --}}
                            <div class="cart-item-img">
                                <a href="{{ route('products.show', $item->product) }}" style="display: block; width: 100%; aspect-ratio: 1; border-radius: 10px; overflow: hidden; background: #f0f0f0; border: 1px solid var(--line);">
                                    @if ($item->product->images->isNotEmpty())
                                        <img src="{{ Storage::url($item->product->images->first()->path) }}" alt="{{ $item->product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <div style="width: 100%; height: 100%; display: grid; place-items: center; color: var(--muted);"><x-icon name="box" /></div>
                                    @endif
                                </a>
                            </div>

                            {{-- جزییات محصول --}}
                            <div class="cart-item-details" style="display: flex; flex-direction: column; justify-content: space-between; gap: 0.75rem;">
                                <div>
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem;">
                                        <h2 style="font-size: 1.05rem; margin: 0; line-height: 1.5;">
                                            <a href="{{ route('products.show', $item->product) }}" style="color: var(--ink); text-decoration: none;">
                                                {{ $item->product->name }}
                                            </a>
                                        </h2>

                                        {{-- دکمه حذف --}}
                                        <form action="{{ route('cart.remove', $item->id) }}" method="POST" onsubmit="return confirm('آیا از حذف این کالا از سبد خرید مطمئن هستید؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-del" title="حذف این کالا" aria-label="حذف" style="background: none; border: none; cursor: pointer; color: var(--danger, #b5473a); padding: 4px; border-radius: 6px; display: inline-flex; align-items: center;">
                                                <x-icon name="trash" style="width: 18px; height: 18px;" />
                                            </button>
                                        </form>
                                    </div>

                                    {{-- مشخصات و تنوع انتخابی (سایز، رنگ، گارانتی و غیره) --}}
                                    @if ($item->price && $item->price->attributeValues->isNotEmpty())
                                        <div style="display: flex; flex-wrap: wrap; gap: 0.35rem; margin-top: 0.5rem;">
                                            @foreach ($item->price->attributeValues as $val)
                                                <span class="badge" style="background: var(--brand-50, #f0f5ee); color: var(--forest, #295d43); font-size: 0.78rem; padding: 2px 8px; border-radius: 6px;">
                                                    {{ $val->attribute?->name ?? 'ویژگی' }}: <strong>{{ $val->value }}</strong>
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                {{-- بخش پایینی: کنترل تعداد و قیمت --}}
                                <div class="cart-item-footer" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; border-top: 1px dashed var(--t-line); padding-top: 0.75rem;">
                                    {{-- فرم ویرایش تعداد --}}
                                    <form action="{{ route('cart.update', $item->id) }}" method="POST" style="display: flex; align-items: center; gap: 6px;">
                                        @csrf
                                        @method('PUT')
                                        <label for="qty_{{ $item->id }}" style="font-size: 0.85rem; color: var(--t-text-soft);">تعداد:</label>
                                        <input type="number" id="qty_{{ $item->id }}" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->price->inventory }}"
                                               style="width: 55px; height: 34px; text-align: center; border: 1px solid var(--line); border-radius: 6px; font-weight: bold; background: #fff;">
                                        <button type="submit" class="btn btn-secondary btn-sm" title="بروزرسانی تعداد" style="padding: 4px 10px; font-size: 0.8rem; height: 34px;">
                                            ثبت
                                        </button>
                                        @if ($item->price->inventory < 5)
                                            <small style="color: var(--danger); font-size: 0.75rem;">(تنها {{ $item->price->inventory }} عدد در انبار)</small>
                                        @endif
                                    </form>

                                    {{-- قیمت کل این ردیف --}}
                                    <div style="text-align: left; line-height: 1.4;">
                                        @if ($item->price->discount > 0)
                                            <div style="font-size: 0.8rem; color: var(--t-text-soft); text-decoration: line-through;">
                                                {{ number_format((float) ($item->price->price * $item->quantity)) }}
                                            </div>
                                        @endif
                                        <div style="font-size: 1.15rem; font-weight: bold; color: var(--forest, #295d43);">
                                            {{ number_format((float) ($item->price->final_price * $item->quantity)) }}
                                            <small style="font-size: 0.75rem; font-weight: normal; color: var(--t-text-soft);">تومان</small>
                                        </div>
                                    </div>
                                </div>
                            </article>
                    @endforeach
                </div>

                {{-- ستون چپ: فاکتور و خلاصه سفارش --}}
                <aside class="cart-summary glass" style="padding: 1.5rem; border-radius: var(--radius-md); background: rgba(255, 255, 255, 0.9); border: 1px solid var(--t-border, #dce4d7); position: sticky; top: 100px;">
                    <h2 style="font-size: 1.15rem; margin: 0 0 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--t-line); color: var(--ink);">
                        خلاصه سفارش
                    </h2>

                    <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.92rem; color: var(--t-text);">
                        <div style="display: flex; justify-content: space-between;">
                            <span>تعداد کالاها:</span>
                            <strong>{{ $cart->items->sum('quantity') }} عدد</strong>
                        </div>

                        @php
                            $rawSubtotal = $cart->items->sum(fn($i) => $i->price->price * $i->quantity);
                            $totalDiscount = $rawSubtotal - $total;
                        @endphp

                        @if ($totalDiscount > 0)
                            <div style="display: flex; justify-content: space-between;">
                                <span>قیمت کل بدون تخفیف:</span>
                                <span>{{ number_format((float) $rawSubtotal) }} تومان</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; color: var(--danger, #b5473a);">
                                <span>سود شما از خرید:</span>
                                <strong>{{ number_format((float) $totalDiscount) }} تومان</strong>
                            </div>
                        @endif

                        <div style="display: flex; justify-content: space-between; font-size: 0.85rem; color: var(--t-text-soft);">
                            <span>هزینه ارسال:</span>
                            <span>محاسبه در مرحله بعد</span>
                        </div>

                        <hr style="border: none; border-top: 1px dashed var(--t-line); margin: 0.5rem 0;">

                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 1.05rem;">
                            <span style="font-weight: bold; color: var(--ink);">مبلغ قابل پرداخت:</span>
                            <div style="color: var(--shop-700, #1d4934); font-weight: 800; font-size: 1.3rem;">
                                {{ number_format((float) $total) }}
                                <small style="font-size: 0.8rem; font-weight: normal; color: var(--t-text-soft);">تومان</small>
                            </div>
                        </div>
                    </div>

                    {{-- دکمه‌های اقدام نهایی --}}
                    <div style="margin-top: 1.5rem; display: flex; flex-direction: column; gap: 0.6rem;">
                        <a href="#" class="btn-glow" style="text-align: center; justify-content: center; width: 100%; padding: 12px; font-size: 1rem; box-sizing: border-box;">
                            تکمیل سفارش و تسویه‌حساب
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary" style="text-align: center; justify-content: center; width: 100%; box-sizing: border-box; font-size: 0.88rem;">
                            بازگشت به فروشگاه و ادامه خرید
                        </a>
                    </div>

                    {{-- نشان‌های اطمینان --}}
                    <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--t-line); display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.8rem; color: var(--t-text-soft);">
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <x-icon name="shield" style="width: 16px; height: 16px; color: var(--forest);" />
                            <span>ضمانت اصالت و سلامت کالا</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <x-icon name="check" style="width: 16px; height: 16px; color: var(--forest);" />
                            <span>امکان بازگشت کالا تا ۷ روز کاری</span>
                        </div>
                    </div>
                </aside>
            </div>
        @endif
    </div>
</main>
@endsection
