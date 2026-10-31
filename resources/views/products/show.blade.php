@extends('layout.master')

@section('title', $product->name)

@section('content')
<main class="product-detail-page">
    <div class="container">
        <nav class="product-breadcrumb" aria-label="مسیر صفحه">
            <a href="{{ route('home') }}">صفحهٔ اصلی</a>
            <span aria-hidden="true">/</span>
            <a href="{{ route('products.index') }}">محصولات</a>
            <span aria-hidden="true">/</span>
            <span aria-current="page">{{ $product->name }}</span>
        </nav>

        <section class="product-detail-card">
            <div class="product-detail-gallery">
                @if ($product->images->isNotEmpty())
                    <div class="product-detail-main-image">
                        <img id="product-main-image" src="{{ Storage::url($product->images->first()->path) }}" alt="{{ $product->name }}">
                    </div>
                    @if ($product->images->count() > 1)
                        <div class="product-detail-thumbnails" aria-label="تصاویر دیگر محصول">
                            @foreach ($product->images as $image)
                                <a class="product-thumbnail {{ $loop->first ? 'is-active' : '' }}" href="{{ Storage::url($image->path) }}" aria-label="نمایش تصویر {{ $loop->iteration }}">
                                    <img src="{{ Storage::url($image->path) }}" alt="">
                                </a>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="product-detail-placeholder" aria-label="این محصول تصویر ندارد">تصویری برای این محصول ثبت نشده است.</div>
                @endif
            </div>

            <div class="product-detail-summary">
                @if ($product->categories->isNotEmpty())
                    <div class="tags-row">
                        @foreach ($product->categories as $category)
                            <span class="tag">{{ $category->full_name }}</span>
                        @endforeach
                    </div>
                @endif
                <h1>{{ $product->name }}</h1>
                <p class="product-detail-description">{{ $product->description }}</p>
                <dl class="product-meta-list">
                    <div><dt>واحد</dt><dd>{{ $product->unit }}</dd></div>
                    <div><dt>وزن</dt><dd>{{ number_format((float) $product->weight, 2) }}</dd></div>
                </dl>
                <a class="btn btn-primary product-back-link" href="{{ route('products.index') }}">بازگشت به محصولات</a>
            </div>
        </section>

        <section class="product-options-section">
            <div class="product-results-heading">
                <div><p class="kicker">انتخاب دلخواه</p><h2>قیمت‌ها و ویژگی‌ها</h2></div>
                <span>{{ $product->prices->count() }} گزینه</span>
            </div>
            @forelse ($product->prices as $price)
                <article class="product-option-card">
                    <div class="product-option-attributes">
                        @forelse ($price->attributeValues as $value)
                            <div class="product-option-attribute">
                                <span>{{ $value->attribute?->name ?? 'ویژگی' }}</span>
                                <strong>
                                    {{ $value->value }}
                                </strong>
                            </div>
                        @empty
                            <span class="color-muted">ویژگی‌ای برای این گزینه ثبت نشده است.</span>
                        @endforelse
                    </div>
                    <div class="product-option-price">
                        @if ($price->discount > 0)
                            <span class="product-old-price">{{ number_format((float) $price->price) }}</span>
                            <span class="product-discount">{{ number_format((float) $price->discount) }}٪ تخفیف</span>
                        @endif
                        <strong>{{ number_format((float) $price->final_price) }} <small>تومان</small></strong>
                    </div>
                    <div class="product-option-stock {{ $price->inventory > 0 ? 'is-available' : 'is-unavailable' }}">
                        {{ $price->inventory > 0 ? 'موجود' : 'ناموجود' }}
                    </div>
                </article>
            @empty
                <div class="product-empty product-empty--compact"><p>هنوز قیمتی برای این محصول ثبت نشده است.</p></div>
            @endforelse
        </section>
    </div>
</main>
@endsection

