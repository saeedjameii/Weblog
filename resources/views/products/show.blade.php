@extends('layout.master')

@section('title', $product->name)

@section('content')
@php
    $minPrice = $product->prices->sortBy('final_price')->first();
    $hasDescription = filled($product->description);
    $hasSpecs = filled($product->unit) || filled($product->weight);
    $inStock = $product->prices->sum('inventory') > 0;
@endphp
<main class="product-detail-page has-buybar">
    <div class="container">
        <nav aria-label="مسیر صفحه">
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}">صفحهٔ اصلی</a></li>
                <li><a href="{{ route('products.index') }}">محصولات</a></li>
                <li aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>
        @if (session('success'))
            <div class="alert alert--ok glass" style="margin-block: 1rem; padding: 0.85rem 1.25rem; border-radius: var(--radius-md); background: rgba(41, 93, 67, 0.1); border: 1px solid var(--forest, #295d43); color: var(--forest-dark, #1d4934); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                <span>{{ session('success') }}</span>
                <a href="{{ route('cart.index') }}" class="btn btn-primary btn-sm" style="padding: 6px 14px; font-size: 0.85rem;">مشاهده سبد خرید</a>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert--err glass" style="margin-block: 1rem; padding: 0.85rem 1.25rem; border-radius: var(--radius-md); background: rgba(181, 71, 58, 0.1); border: 1px solid var(--danger, #b5473a); color: var(--danger, #b5473a);">
                <ul style="margin: 0; padding-inline-start: 1.2rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="pdp">
            <div class="gallery glass">
                @if ($product->images->isNotEmpty())
                    <div class="frame frame--product-tall">
                        <img id="product-main-image" src="{{ Storage::url($product->images->first()->path) }}" alt="{{ $product->name }}" width="800" height="1000" decoding="async">
                    </div>
                    @if ($product->images->count() > 1)
                        <div class="gallery__thumbs" aria-label="تصاویر دیگر محصول">
                            @foreach ($product->images as $image)
                                <button type="button" class="product-thumbnail {{ $loop->first ? 'is-active' : '' }}"
                                    data-product-thumbnail="{{ Storage::url($image->path) }}"
                                    aria-label="نمایش تصویر {{ $loop->iteration }}">
                                    <span class="frame frame--thumb"><img src="{{ Storage::url($image->path) }}" alt="" width="72" height="72" loading="lazy" decoding="async"></span>
                                </button>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="frame frame--product-tall frame--empty" role="img" aria-label="این محصول تصویر ندارد"></div>
                @endif
            </div>

            <div class="pdp__info glass">
                @if ($product->categories->isNotEmpty())
                    <div class="badges">
                        @foreach ($product->categories as $category)
                            <span class="badge badge--shop">{{ $category->full_name }}</span>
                        @endforeach
                    </div>
                @endif
                <h1 class="pdp__title">{{ $product->name }}</h1>

                @if ($minPrice)
                    <div class="pdp__price">
                        <small>شروع قیمت از</small>
                        <strong>{{ number_format((float) $minPrice->final_price) }} تومان</strong>
                    </div>
                @endif
                @if ($product->prices->isNotEmpty())
                    <div class="badges">
                        <span class="badge {{ $inStock ? 'badge--ok' : 'badge--err' }}">{{ $inStock ? 'موجود' : 'ناموجود' }}</span>
                        @if ($minPrice && $minPrice->discount > 0)
                            <span class="badge badge--shop">{{ (int) $minPrice->discount }}٪ تخفیف</span>
                        @endif
                    </div>
                @endif

                <dl class="meta-list">
                    <div><dt>واحد</dt><dd>{{ $product->unit }}</dd></div>
                    <div><dt>وزن</dt><dd>{{ number_format((float) $product->weight, 2) }}</dd></div>
                </dl>

                <div class="pdp__actions">
                    <a class="btn-glow" href="#product-options">مشاهده گزینه‌ها و قیمت‌ها</a>
                    <a class="btn-soft" href="{{ route('products.index') }}">بازگشت به محصولات</a>
                </div>
            </div>
        </section>

        @if ($hasDescription || $hasSpecs)
            <section class="sx-panel glass" data-tabs>
                <div class="tabs__list" role="tablist" aria-label="اطلاعات محصول" hidden>
                    @if ($hasDescription)
                        <button type="button" class="tabs__tab" role="tab" id="tab-desc" aria-controls="panel-desc" aria-selected="true">توضیحات</button>
                    @endif
                    @if ($hasSpecs)
                        <button type="button" class="tabs__tab" role="tab" id="tab-specs" aria-controls="panel-specs" aria-selected="{{ $hasDescription ? 'false' : 'true' }}">مشخصات</button>
                    @endif
                </div>
                @if ($hasDescription)
                    <div class="tabs__panel" role="tabpanel" id="panel-desc" aria-labelledby="tab-desc">
                        <h2>توضیحات</h2>
                        <p >{{ $product->description }}</p>
                    </div>
                @endif
                @if ($hasSpecs)
                    <div class="tabs__panel" role="tabpanel" id="panel-specs" aria-labelledby="tab-specs">
                        <h2>مشخصات</h2>
                        <div class="table-wrap">
                            <table class="spec-table">
                                <tbody>
                                    <tr><th scope="row">واحد</th><td>{{ $product->unit }}</td></tr>
                                    <tr><th scope="row">وزن</th><td>{{ number_format((float) $product->weight, 2) }}</td></tr>

                                    
                                    @php
                                        $specifications = $product->prices
                                            ->flatMap(fn ($price) => $price->attributeValues)
                                            ->groupBy(fn ($value) => $value->attribute?->name ?? 'ویژگی');
                                    @endphp
                                    @foreach ($specifications as $attributeName => $values)
                                        <tr>
                                            <th scope="row">{{ $attributeName }}</th>
                                            <td>{{ $values->pluck('value')->unique()->implode(', ') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </section>
        @endif

        <section id="product-options" class="sx-panel glass">
            <div class="sx-head">
                <div><span class="sx-kicker sx-kicker--shop">انتخاب دلخواه</span><h2 class="sx-title">قیمت‌ها و ویژگی‌ها</h2></div>
                <span class="badge badge--shop">{{ $product->prices->count() }} گزینه</span>
            </div>
            <div class="option-list">
                @forelse ($product->prices as $price)
                    <article class="option-card">
                        <div class="option-attrs">
                            @forelse ($price->attributeValues as $value)
                                <div class="option-attr">
                                    <span>{{ $value->attribute?->name ?? 'ویژگی' }}</span>
                                    <strong>
                                        {{ $value->value }}
                                    </strong>
                                </div>
                            @empty
                                <span >ویژگی‌ای برای این گزینه ثبت نشده است.</span>
                            @endforelse
                        </div>
                        <div class="option-price">
                            @if ($price->discount > 0)
                                <span class="option-old">{{ number_format((float) $price->price) }}</span>
                                <span class="badge badge--shop">{{ number_format((float) $price->discount) }}٪ تخفیف</span>
                            @endif
                            <strong>{{ number_format((float) $price->final_price) }} <small>تومان</small></strong>
                        </div>
                        <div class="option-actions" style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.5rem;">
                            <span class="badge {{ $price->inventory > 0 ? 'badge--ok' : 'badge--err' }}">
                                {{ $price->inventory > 0 ? 'موجود در انبار (' . $price->inventory . ')' : 'ناموجود' }}
                            </span>
                            @if ($price->inventory > 0)
                                <form action="{{ route('cart.add') }}" method="POST" style="display: flex; align-items: center; gap: 0.5rem;">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="price_id" value="{{ $price->id }}">
                                    <input type="number" name="quantity" value="1" min="1" max="{{ $price->inventory }}"
                                           style="width: 50px; height: 36px; text-align: center; border: 1px solid var(--t-border, #dce4d7); border-radius: 8px; font-weight: 600; background: #fff;" aria-label="تعداد">
                                    <button type="submit" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px;">
                                        <x-icon name="cart" />
                                        <span>افزودن به سبد</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="sx-empty"><p>هنوز قیمتی برای این محصول ثبت نشده است.</p></div>
                @endforelse
            </div>
        </section>
    </div>

    @if ($minPrice)
        <div class="buybar glass-strong">
            <div class="buybar__price"><small>شروع قیمت از</small><strong>{{ number_format((float) $minPrice->final_price) }} تومان</strong></div>
            <a class="btn-glow" href="#product-options">مشاهده گزینه‌ها</a>
        </div>
    @endif
</main>
@endsection
