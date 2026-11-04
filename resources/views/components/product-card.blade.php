@props(['product'])
@php
    $minPrice = $product->prices->sortBy('final_price')->first();
    $hasDiscount = $minPrice && $minPrice->discount > 0;
    $soldOut = $product->prices->isNotEmpty() && $product->prices->sum('inventory') <= 0;
    $image = $product->images->first();
    $category = $product->categories->first();
@endphp
<article class="pcard pcard--product lift">
    <div class="pcard__media">
        @if ($image)
            <div class="frame frame--product is-loading">
                <img src="{{ Storage::url($image->path) }}" alt="{{ $product->name }}" width="480" height="480" loading="lazy" decoding="async">
            </div>
        @else
            <div class="frame frame--product frame--empty" role="img" aria-label="این محصول تصویر ندارد"></div>
        @endif
        @if ($hasDiscount || $soldOut)
            <div class="pcard__badges badges">
                @if ($hasDiscount)<span class="badge badge--shop">{{ (int) $minPrice->discount }}٪ تخفیف</span>@endif
                @if ($soldOut)<span class="badge badge--err">ناموجود</span>@endif
            </div>
        @endif
    </div>
    <div class="pcard__body">
        @if ($category)<span class="pcard__cat">{{ $category->full_name }}</span>@endif
        <h3 class="pcard__title"><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></h3>
        <div class="pcard__price">
            @if ($minPrice)
                @if ($hasDiscount)<span class="pcard__old">{{ number_format((float) $minPrice->price) }}</span>@endif
                <small>شروع از</small>
                <strong>{{ number_format((float) $minPrice->final_price) }} <small>تومان</small></strong>
            @else
                <small>قیمت</small>
                <strong>به زودی</strong>
            @endif
        </div>
    </div>
</article>
