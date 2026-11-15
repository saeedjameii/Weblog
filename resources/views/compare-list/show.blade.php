@extends('layout.master')

@section('title', 'مقایسه محصولات')

@section('content')
@php
    $products = $compareList->pluck('product')->filter();
    $attributeNames = $products
        ->flatMap(fn ($product) => $product->prices->flatMap(fn ($price) => $price->attributeValues))
        ->map(fn ($value) => $value->attribute?->name)
        ->filter()
        ->unique()
        ->values();

    $specifications = collect([
        ['key' => 'unit', 'label' => 'واحد', 'value' => fn ($product) => $product->unit],
        ['key' => 'weight', 'label' => 'وزن', 'value' => fn ($product) => filled($product->weight) ? number_format((float) $product->weight, 2) : null],
    ]);
@endphp

<main class="compare-page">
    <div class="container">
        <nav aria-label="مسیر صفحه">
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">صفحهٔ اصلی</a></li>
                <li><a href="{{ route('products.index') }}">فروشگاه</a></li>
                <li aria-current="page">مقایسهٔ محصولات</li>
            </ol>
        </nav>

        <section class="compare-heading glass">
            <div>
                <span class="sx-kicker sx-kicker--shop"><x-icon name="compare" /> انتخاب هوشمندانه‌تر</span>
                <h1 class="sx-title">مقایسهٔ محصولات</h1>
                <p class="sx-lead">قیمت، ویژگی‌ها و گزینه‌های هر محصول را کنار هم ببینید.</p>
            </div>
            <div class="compare-heading__actions">
                <span class="badge badge--shop">{{ $products->count() }} از ۴ محصول</span>
                <a class="btn-soft" href="{{ route('products.index') }}"><x-icon name="shop" /> افزودن محصول</a>
            </div>
        </section>

        @if ($products->isEmpty())
            <section class="compare-empty glass">
                <span class="compare-empty__icon"><x-icon name="compare" /></span>
                <h2>هنوز محصولی برای مقایسه انتخاب نکرده‌اید</h2>
                <p>از فروشگاه چند محصول انتخاب کنید تا قیمت و مشخصاتشان را کنار هم ببینید.</p>
                <a class="btn-glow" href="{{ route('products.index') }}"><x-icon name="shop" /> رفتن به فروشگاه</a>
            </section>
        @else
            <section class="compare-ai glass" style="margin-bottom: 24px; padding: 22px; border-radius: 18px; border: 1px solid var(--t-line); background: rgba(255, 255, 255, 0.85); box-shadow: var(--shadow-sm);">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
                    <div>
                        <span class="sx-kicker sx-kicker--shop"><x-icon name="compare" /> دستیار هوشمند خرید</span>
                        <h2 style="font-size: 1.3rem; margin: 6px 0 2px 0; color: var(--t-text);">تحلیل و قضاوت هوش مصنوعی</h2>
                        <p style="margin: 0; color: var(--t-text-soft); font-size: 0.92rem;">نقاط قوت، معایب و پیشنهاد نهایی بر اساس مشخصات و بررسی‌های بازار</p>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button type="button" class="btn-soft" id="btnAiQuick" data-search="0">
                            ⚡ تحلیل سریع
                        </button>
                        <button type="button" class="btn-glow" id="btnAiDeep" data-search="1">
                            🌐 تحلیل عمیق کارشناسی
                        </button>
                    </div>
                </div>

                <!-- Loading State -->
                <div id="aiLoadingBox" style="display: none; padding: 25px; text-align: center;">
                    <div style="display: inline-block; color: var(--brand-700); font-weight: bold; font-size: 1rem;">
                        <span id="aiLoadingText">در حال پردازش مشخصات توسط هوش مصنوعی...</span>
                    </div>
                </div>

                <!-- Results Box -->
                <div id="aiResultsBox" style="display: none; margin-top: 20px; border-top: 1px solid var(--t-line); padding-top: 18px;">
                    <div style="background: var(--brand-50, #eef2ff); border: 1px solid var(--brand-300, #a5b4fc); padding: 16px; border-radius: 14px; margin-bottom: 18px;">
                        <strong style="color: var(--brand-700, #3f3fa8); display: block; margin-bottom: 6px; font-size: 1rem;">💡 جمع‌بندی و نتیجه‌گیری نهایی:</strong>
                        <p id="aiVerdict" style="margin: 0; line-height: 1.85; color: var(--t-text, #0f172a); font-size: 0.96rem;"></p>
                    </div>

                    <div id="aiProductsGrid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px;">
                        <!-- کارتهای مزایا و معایب اینجا با جاوااسکریپت تزریق می‌شوند -->
                    </div>
                </div>
            </section>
            <section class="compare-board glass" aria-label="جدول مقایسه محصولات">
                <div class="compare-board__scroll" tabindex="0" aria-label="برای دیدن همهٔ محصولات به‌صورت افقی پیمایش کنید">
                    <table class="compare-table">
                        <thead>
                            <tr>
                                <th class="compare-table__label compare-table__label--head" scope="col">
                                    <span class="compare-table__eyebrow">{{ $products->count() }} انتخاب شما</span>
                                    <strong>مشخصات</strong>
                                </th>
                                @foreach ($products as $product)
                                    @php
                                        $image = $product->images->first();
                                        $minPrice = $product->prices->sortBy('final_price')->first();
                                        $inStock = $product->prices->sum('inventory') > 0;
                                    @endphp
                                    <th class="compare-product" scope="col">
                                        <button type="button" class="compare-product__remove" data-compare-remove
                                            data-product-id="{{ $product->id }}" data-remove-url="{{ route('compareList.destroy') }}"
                                            aria-label="حذف {{ $product->name }} از مقایسه" title="حذف از مقایسه">
                                            <x-icon name="close" />
                                        </button>
                                        <a class="compare-product__image" href="{{ route('products.show', $product) }}">
                                            @if ($image)
                                                <img src="{{ Storage::url($image->path) }}" alt="{{ $product->name }}" loading="lazy" decoding="async">
                                            @else
                                                <span class="compare-product__placeholder"><x-icon name="image" /></span>
                                            @endif
                                        </a>
                                        @if ($product->categories->isNotEmpty())
                                            <span class="compare-product__category">{{ $product->categories->first()->full_name }}</span>
                                        @endif
                                        <a class="compare-product__name" href="{{ route('products.show', $product) }}">{{ $product->name }}</a>
                                        <div class="compare-product__price">
                                            @if ($minPrice)
                                                <strong>{{ number_format((float) $minPrice->final_price) }}</strong>
                                                <span>تومان</span>
                                                @if ($minPrice->discount > 0)
                                                    <small>{{ number_format((float) $minPrice->discount) }}٪ تخفیف</small>
                                                @endif
                                            @else
                                                <strong class="compare-muted">قیمت ثبت نشده</strong>
                                            @endif
                                        </div>
                                        <span class="badge {{ $inStock ? 'badge--ok' : 'badge--err' }}">
                                            {{ $inStock ? 'موجود' : 'ناموجود' }}
                                        </span>
                                        <a class="compare-product__link" href="{{ route('products.show', $product) }}">مشاهدهٔ محصول <x-icon name="arrow" /></a>
                                    </th>
                                @endforeach
                                @if ($products->count() < 4)
                                    <th class="compare-add" scope="col">
                                        <a href="{{ route('products.index') }}">
                                            <span class="compare-add__icon">+</span>
                                            <strong>محصول دیگری اضافه کنید</strong>
                                            <small>برای مقایسهٔ کامل‌تر</small>
                                        </a>
                                    </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="compare-section-row"><th colspan="{{ 1 + $products->count() + ($products->count() < 4 ? 1 : 0) }}">اطلاعات کلی</th></tr>

                            @foreach ($specifications as $specification)
                                @php
                                    $hasValue = $products->contains(
                                        fn ($product) => filled(($specification['value'])($product))
                                    );
                                @endphp

                                @if ($hasValue)
                                    <tr>
                                        <th class="compare-table__label" scope="row">
                                            {{ $specification['label'] }}
                                        </th>

                                        @foreach ($products as $product)
                                            @php
                                                $value = ($specification['value'])($product);
                                            @endphp

                                            <td>
                                                {{ filled($value) ? $value : '—' }}
                                                {{ $specification['key'] === 'weight' && filled($value) ? ' کیلوگرم' : '' }}
                                            </td>
                                        @endforeach

                                        @if ($products->count() < 4)
                                            <td class="compare-add-cell"></td>
                                        @endif
                                    </tr>
                                @endif
                            @endforeach

                            @foreach ($attributeNames as $attributeName)
                                <tr>
                                    <th class="compare-table__label" scope="row">{{ $attributeName }}</th>

                                    @foreach ($products as $product)
                                        @php
                                            $values = $product->prices
                                                ->flatMap(fn ($price) => $price->attributeValues)
                                                ->filter(fn ($value) => $value->attribute?->name === $attributeName)
                                                ->pluck('value')
                                                ->filter()
                                                ->unique();
                                        @endphp

                                        <td>
                                            @if ($values->isNotEmpty())
                                                <div class="compare-values">
                                                    <span>{{ $values->implode(', ') }}</span>
                                                </div>
                                            @else
                                                <span class="compare-muted">—</span>
                                            @endif
                                        </td>
                                    @endforeach

                                    @if ($products->count() < 4)
                                        <td class="compare-add-cell"></td>
                                    @endif
                                </tr>
                            @endforeach


                            <tr class="compare-section-row"><th colspan="{{ 1 + $products->count() + ($products->count() < 4 ? 1 : 0) }}">گزینه‌ها و قیمت‌ها</th></tr>
                            <tr>
                                <th class="compare-table__label" scope="row">گزینه‌های قابل خرید</th>
                                @foreach ($products as $product)
                                    <td>
                                        @if ($product->prices->isEmpty())
                                            <span class="compare-muted">گزینه‌ای ثبت نشده</span>
                                        @else
                                            <div class="compare-options">
                                                @foreach ($product->prices->sortBy('final_price') as $price)
                                                    <div class="compare-option">
                                                        <div class="compare-option__values">
                                                            @forelse ($price->attributeValues as $value)
                                                                <span><b>{{ $value->attribute?->name ?? 'ویژگی' }}:</b> {{ $value->value }}</span>
                                                            @empty
                                                                <span>گزینهٔ پایه</span>
                                                            @endforelse
                                                        </div>
                                                        <strong>{{ number_format((float) $price->final_price) }} <small>تومان</small></strong>
                                                        @if ($price->discount > 0)<del>{{ number_format((float) $price->price) }}</del>@endif
                                                        <span class="badge {{ $price->inventory > 0 ? 'badge--ok' : 'badge--err' }}">{{ $price->inventory > 0 ? 'موجود' : 'ناموجود' }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                                @if ($products->count() < 4)<td class="compare-add-cell"></td>@endif
                            </tr>
                            @if ($products->contains(fn ($product) => filled($product->description)))
                                <tr class="compare-section-row"><th colspan="{{ 1 + $products->count() + ($products->count() < 4 ? 1 : 0) }}">دربارهٔ محصول</th></tr>
                                <tr>
                                    <th class="compare-table__label" scope="row">توضیحات</th>
                                    @foreach ($products as $product)
                                        <td class="compare-description">{{ filled($product->description) ? $product->description : 'توضیحی ثبت نشده' }}</td>
                                    @endforeach
                                    @if ($products->count() < 4)<td class="compare-add-cell"></td>@endif
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <p class="compare-board__hint"><x-icon name="arrow" /> جدول را به‌صورت افقی پیمایش کنید تا همهٔ محصولات را ببینید.</p>
            </section>
        @endif
    </div>
</main>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-compare-remove]').forEach((button) => {
        button.addEventListener('click', async () => {
            if (button.disabled) return;
            button.disabled = true;
            try {
                const response = await fetch(button.dataset.removeUrl, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                    },
                    body: JSON.stringify({ product_id: button.dataset.productId }),
                });
                if (!response.ok) throw new Error('حذف محصول انجام نشد. دوباره تلاش کنید.');
                window.location.reload();
            } catch (error) {
                button.disabled = false;
                window.alert(error.message);
            }
        });
    });
</script>
<script>
    const setupAiButtons = () => {
        const loadingBox = document.getElementById('aiLoadingBox');
        const loadingText = document.getElementById('aiLoadingText');
        const resultsBox = document.getElementById('aiResultsBox');
        const verdictEl = document.getElementById('aiVerdict');
        const gridEl = document.getElementById('aiProductsGrid');
        const btnQuick = document.getElementById('btnAiQuick');
        const btnDeep = document.getElementById('btnAiDeep');

        const requestAi = async (withWebSearch) => {
            if (!loadingBox) return;

            btnQuick.disabled = true;
            btnDeep.disabled = true;
            resultsBox.style.display = 'none';
            loadingBox.style.display = 'block';
            loadingText.textContent = withWebSearch 
                ? '🌐 در حال تحلیل عمیق بر اساس نقد و بررسی‌ها و تست‌های بازار...' 
                : '⚡ در حال پردازش مشخصات و مقایسه...';

            try {
                const response = await fetch("{{ route('compareList.ai') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                    },
                    body: JSON.stringify({ web_search: withWebSearch })
                });

                const json = await response.json();
                if (!response.ok) throw new Error(json.message || 'خطا در ارتباط با سرور');

                const data = json.data;
                verdictEl.textContent = data.verdict || data.summary || 'تحلیلی در دسترس نیست.';

                gridEl.innerHTML = '';
                (data.products || []).forEach(p => {
                    const card = document.createElement('div');
                    card.style.cssText = 'background: #ffffff; border: 1px solid var(--t-line, #e2e8f0); border-radius: 14px; padding: 16px; box-shadow: var(--shadow-sm, 0 1px 3px rgba(0,0,0,0.05));';
                    
                    const prosList = (p.pros || []).map(item => `<li style="margin-bottom: 4px; color: var(--t-text-soft, #334155);">${item}</li>`).join('');
                    const consList = (p.cons || []).map(item => `<li style="margin-bottom: 4px; color: var(--t-text-soft, #334155);">${item}</li>`).join('');

                    card.innerHTML = `
                        ${p.name ? `<h4 style="margin: 0 0 10px 0; font-size: 1.02rem; font-weight: 700; color: var(--t-text, #0f172a); border-bottom: 1px solid var(--t-line, #e2e8f0); padding-bottom: 8px;">${p.name}</h4>` : ''}
                        <div style="font-weight: 600; color: var(--brand-700, #3f3fa8); font-size: 0.92rem; margin-bottom: 10px; background: rgba(91,91,214,0.07); padding: 8px 10px; border-radius: 8px;">
                            🎯 مناسب برای: <span style="font-weight: normal; color: var(--t-text, #0f172a);">${p.best_for || 'عموم کاربران'}</span>
                        </div>
                        <div style="margin-top: 10px;">
                            <span style="color: #15803d; font-size: 0.88rem; font-weight: bold; display: flex; align-items: center; gap: 4px;">
                                <span style="display:inline-block; width:6px; height:6px; background:#15803d; border-radius:50%;"></span>
                                نقاط قوت:
                            </span>
                            <ul style="padding-right: 18px; margin: 6px 0; font-size: 0.88rem; line-height: 1.7;">${prosList || '<li style="color: var(--t-text-soft);">موردی ثبت نشده</li>'}</ul>
                        </div>
                        <div style="margin-top: 10px;">
                            <span style="color: #b91c1c; font-size: 0.88rem; font-weight: bold; display: flex; align-items: center; gap: 4px;">
                                <span style="display:inline-block; width:6px; height:6px; background:#b91c1c; border-radius:50%;"></span>
                                نقاط ضعف:
                            </span>
                            <ul style="padding-right: 18px; margin: 6px 0; font-size: 0.88rem; line-height: 1.7;">${consList || '<li style="color: var(--t-text-soft);">موردی ثبت نشده</li>'}</ul>
                        </div>
                    `;
                    gridEl.appendChild(card);
                });

                resultsBox.style.display = 'block';
            } catch (err) {
                alert(err.message);
            } finally {
                loadingBox.style.display = 'none';
                btnQuick.disabled = false;
                btnDeep.disabled = false;
            }
        };

        btnQuick?.addEventListener('click', () => requestAi(false));
        btnDeep?.addEventListener('click', () => requestAi(true));
    };

    document.addEventListener('DOMContentLoaded', setupAiButtons);
</script>
@endpush
