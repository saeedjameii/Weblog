@extends('layout.panel')

@section('title', 'جزئیات سفارش ' . $order->tracking_code)

@section('content')
<main class="page-area">
    <div class="main-content">
        <section class="intro intro--header">
            <div>
                <p class="kicker">مدیریت سفارش‌ها</p>
                <h1 class="display-font panel-title">سفارش {{ $order->tracking_code }}</h1>
            </div>
            <a class="button button-secondary" href="{{ route('panel.orders.index') }}">
                ← بازگشت به لیست سفارش‌ها
            </a>
        </section>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- چیدمان دو ستونه: مشخصات گیرنده و فرم مدیریت وضعیت --}}
        <div class="order-detail-grid">
            {{-- کارت مشخصات تحویل‌گیرنده --}}
            <section class="order-detail-card">
                <h2 class="order-card-title">📍 مشخصات تحویل‌گیرنده و آدرس</h2>

                <div class="order-info-list">
                    <div class="order-info-row">
                        <span class="color-muted">تاریخ و زمان ثبت:</span>
                        <strong>{{ $order->created_at_jalali }}</strong>
                    </div>
                    <div class="order-info-row">
                        <span class="color-muted">نام گیرنده:</span>
                        <strong>{{ $order->recipient_name }}</strong>
                    </div>
                    <div class="order-info-row">
                        <span class="color-muted">شماره تماس:</span>
                        <strong>{{ $order->recipient_phone }}</strong>
                    </div>
                    <div class="order-info-row">
                        <span class="color-muted">حساب کاربری:</span>
                        @if ($order->user)
                            <span>{{ $order->user->first_name }} {{ $order->user->last_name }} ({{ $order->user->email }})</span>
                        @else
                            <span class="color-muted">کاربر مهمان</span>
                        @endif
                    </div>
                    <div class="order-info-row">
                        <span class="color-muted">استان و شهر:</span>
                        <span>{{ $order->province }} — {{ $order->city }}</span>
                    </div>
                    <div class="order-info-row">
                        <span class="color-muted">کد پستی ۱۰ رقمی:</span>
                        <span class="order-code-font">{{ $order->postal_code }}</span>
                    </div>
                    <div class="order-info-row order-info-row--full">
                        <span class="color-muted">نشانی دقیق پستی:</span>
                        <p class="order-address-text">{{ $order->address }}</p>
                    </div>
                    @if ($order->note)
                        <div class="order-info-row order-info-row--full">
                            <span class="color-muted">یادداشت مشتری:</span>
                            <p class="order-note-text">{{ $order->note }}</p>
                        </div>
                    @endif
                </div>
            </section>

            {{-- کارت تغییر وضعیت و مدیریت سفارش --}}
            <section class="order-detail-card">
                <h2 class="order-card-title">⚙️ وضعیت سفارش و پرداخت</h2>

                <form action="{{ route('panel.orders.updateStatus', $order) }}" method="POST" class="order-status-form">
                    @csrf

                    <div class="form-group">
                        <label for="status" class="form-label">وضعیت فرآیند سفارش:</label>
                        <select name="status" id="status" class="select order-full-select" required>
                            <option value="pending" @selected($order->status === 'pending')>⏳ در انتظار بررسی / پرداخت</option>
                            <option value="processing" @selected($order->status === 'processing')>⚙️ در حال پردازش و بسته‌بندی</option>
                            <option value="shipped" @selected($order->status === 'shipped')>🚚 تحویل به پست / ارسال شده</option>
                            <option value="delivered" @selected($order->status === 'delivered')>✅ تحویل داده شده به مشتری</option>
                            <option value="cancelled" @selected($order->status === 'cancelled')>❌ لغو شده (بازگشت موجودی به انبار)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="payment_status" class="form-label">وضعیت پرداخت فاکتور:</label>
                        <select name="payment_status" id="payment_status" class="select order-full-select" required>
                            <option value="unpaid" @selected($order->payment_status === 'unpaid')>⚠️ پرداخت نشده</option>
                            <option value="paid" @selected($order->payment_status === 'paid')>💳 پرداخت شده</option>
                            <option value="refunded" @selected($order->payment_status === 'refunded')>↩️ استرداد وجه</option>
                        </select>
                    </div>

                    <p class="order-hint-text color-muted">
                        💡 نکته: در صورتی که سفارش را به وضعیت «لغو شده» تغییر دهید، موجودی تمام اقلام به صورت خودکار به انبار بازگردانده می‌شود.
                    </p>

                    <button type="submit" class="button button-primary order-btn-block">
                        ذخیره و اعمال وضعیت جدید
                    </button>
                </form>
            </section>
        </div>

        {{-- جدول اقلام فاکتور سفارش --}}
        <section class="order-items-section">
            <h2 class="order-card-title">📦 اقلام خریداری‌شده در این سفارش</h2>

            <div class="order-table-responsive">
                <table class="order-items-table">
                    <thead>
                        <tr>
                            <th>تصویر</th>
                            <th>نام محصول</th>
                            <th>تنوع و مشخصات</th>
                            <th>قیمت واحد</th>
                            <th>تعداد</th>
                            <th>قیمت کل ردیف</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="order-item-img-cell">
                                    @if ($item->product && $item->product->images->isNotEmpty())
                                        <img src="{{ Storage::url($item->product->images->first()->path) }}"
                                             alt="{{ $item->product_name }}" class="order-item-thumb">
                                    @else
                                        <div class="order-item-thumb order-item-thumb--empty">🛍️</div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $item->product_name }}</strong>
                                    @if ($item->product)
                                        <a href="{{ route('products.show', $item->product) }}" target="_blank" class="order-product-link">
                                            (مشاهده کالا در فروشگاه ↗)
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->variant_name)
                                        <span class="tag">{{ $item->variant_name }}</span>
                                    @else
                                        <span class="color-muted">بدون تنوع</span>
                                    @endif
                                </td>
                                <td>{{ number_format((float) $item->unit_price) }} تومان</td>
                                <td><strong>{{ $item->quantity }}</strong></td>
                                <td><strong>{{ number_format((float) $item->total_price) }} تومان</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- فاکتور مالی --}}
            <div class="order-financial-summary">
                <div class="order-summary-row">
                    <span>جمع اقلام سفارش:</span>
                    <span>{{ number_format((float) $order->items->sum('total_price')) }} تومان</span>
                </div>
                <div class="order-summary-row">
                    <span>هزینه ارسال:</span>
                    <span>{{ $order->shipping_cost == 0 ? 'رایگان' : number_format((float) $order->shipping_cost) . ' تومان' }}</span>
                </div>
                @if ($order->discount_amount > 0)
                    <div class="order-summary-row order-summary-row--discount">
                        <span>تخفیف ({{ $order->discount_code ?: 'کوپن' }}):</span>
                        <span>{{ number_format((float) $order->discount_amount) }} - تومان</span>
                    </div>
                @endif
                <div class="order-summary-row order-summary-row--total">
                    <span>مبلغ نهایی فاکتور:</span>
                    <strong class="order-price-val">{{ number_format((float) $order->total_amount) }} تومان</strong>
                </div>
                <div class="order-summary-row order-summary-row--method">
                    <span>روش پرداخت:</span>
                    <span class="tag">{{ $order->payment_method === 'online' ? 'درگاه آنلاین' : $order->payment_method }}</span>
                </div>
            </div>
        </section>
    </div>
</main>
@endsection
