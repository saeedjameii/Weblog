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
                        <div class="{{ $price->inventory > 0 ? 'is-available' : 'is-unavailable' }}">
                            <span class="badge {{ $price->inventory > 0 ? 'badge--ok' : 'badge--err' }}">{{ $price->inventory > 0 ? 'موجود' : 'ناموجود' }}</span>
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
