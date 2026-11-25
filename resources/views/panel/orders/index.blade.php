@extends('layout.panel')

@section('title', 'مدیریت سفارش‌ها')

@section('content')
<main class="page-area">
    <div class="main-content">
        <section class="intro intro--header">
            <div>
                <p class="kicker">مدیریت فروشگاه</p>
                <h1 class="display-font panel-title">سفارش‌های مشتریان</h1>
            </div>
            @if ($pendingCount > 0)
                <span class="tag tag--warn tag--count">
                    {{ $pendingCount }} سفارش در انتظار بررسی
                </span>
            @endif
        </section>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        {{-- نوار جستجو و فیلترهای پیشرفته --}}
        <form class="order-filter-bar" method="GET" action="{{ route('panel.orders.index') }}">
            <div class="order-filter-field">
                <input class="input order-filter-search" type="search" name="search"
                       value="{{ request('search') }}"
                       placeholder="جست‌وجوی کد رهگیری، نام، تلفن یا آدرس...">
            </div>

            <div class="order-filter-selects">
                <select class="select" name="status">
                    <option value="">همه وضعیت‌های سفارش</option>
                    <option value="pending" @selected(request('status') === 'pending')>⏳ در انتظار بررسی</option>
                    <option value="processing" @selected(request('status') === 'processing')>⚙️ در حال پردازش</option>
                    <option value="shipped" @selected(request('status') === 'shipped')>🚚 ارسال شده</option>
                    <option value="delivered" @selected(request('status') === 'delivered')>✅ تحویل شده</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>❌ لغو شده</option>
                </select>

                <select class="select" name="payment_status">
                    <option value="">همه وضعیت‌های پرداخت</option>
                    <option value="paid" @selected(request('payment_status') === 'paid')>💳 پرداخت شده</option>
                    <option value="unpaid" @selected(request('payment_status') === 'unpaid')>⚠️ پرداخت نشده</option>
                    <option value="refunded" @selected(request('payment_status') === 'refunded')>↩️ بازگشت وجه</option>
                </select>

                <button class="button button-primary" type="submit">فیلتر</button>

                @if (request()->hasAny(['search', 'status', 'payment_status']))
                    <a class="button button-secondary" href="{{ route('panel.orders.index') }}">پاکسازی</a>
                @endif
            </div>
        </form>

        {{-- لیست سفارش‌ها --}}
        <div class="order-admin-list">
            @forelse ($orders as $order)
                <article class="order-admin-card">
                    <div class="order-admin-header">
                        <div class="order-admin-tracking">
                            <span class="order-tracking-label">کد پیگیری:</span>
                            <strong class="order-tracking-code">{{ $order->tracking_code }}</strong>
                        </div>
                        <div class="order-admin-badges">
                            @if ($order->status === 'pending')
                                <span class="tag tag--warn">در انتظار بررسی</span>
                            @elseif ($order->status === 'processing')
                                <span class="tag tag--info">در حال پردازش</span>
                            @elseif ($order->status === 'shipped')
                                <span class="tag tag--brand">ارسال شده</span>
                            @elseif ($order->status === 'delivered')
                                <span class="tag tag--ok">تحویل شده</span>
                            @elseif ($order->status === 'cancelled')
                                <span class="tag tag--err">لغو شده</span>
                            @endif

                            @if ($order->payment_status === 'paid')
                                <span class="tag tag--ok">پرداخت شده</span>
                            @elseif ($order->payment_status === 'unpaid')
                                <span class="tag tag--err">پرداخت نشده</span>
                            @elseif ($order->payment_status === 'refunded')
                                <span class="tag tag--warn">مسترد شده</span>
                            @endif
                        </div>
                    </div>

                    <div class="order-admin-body">
                        <div class="order-admin-customer">
                            <strong>{{ $order->recipient_name }}</strong>
                            <span class="color-muted">{{ $order->recipient_phone }}</span>
                            <p class="order-admin-address color-muted">{{ $order->province }}، {{ $order->city }} — {{ Str::limit($order->address, 65) }}</p>
                        </div>

                        <div class="order-admin-meta">
                            <div class="order-admin-stat">
                                <span class="color-muted">تعداد اقلام:</span>
                                <strong>{{ $order->items->sum('quantity') }} عدد</strong>
                            </div>
                            <div class="order-admin-stat">
                                <span class="color-muted">مبلغ کل فاکتور:</span>
                                <strong class="order-price-val">{{ number_format((float) $order->total_amount) }} تومان</strong>
                            </div>
                            <div class="order-admin-stat">
                                <span class="color-muted">تاریخ ثبت:</span>
                                <span class="color-muted">{{ $order->created_at_jalali }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="order-admin-footer">
                        <a class="button button-primary button-small" href="{{ route('panel.orders.show', $order) }}">
                            مشاهده و مدیریت جزئیات فاکتور
                        </a>
                    </div>
                </article>
            @empty
                <div class="empty-state">
                    <h2>سفارشی یافت نشد</h2>
                    <p class="color-muted">با فیلترهای فعلی هیچ سفارشی مطابقت ندارد یا هنوز سفارشی ثبت نشده است.</p>
                </div>
            @endforelse
        </div>

        {{-- صفحه‌بندی --}}
        {{ $orders->links('pagination.custom') }}
    </div>
</main>
@endsection
