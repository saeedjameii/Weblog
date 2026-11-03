<div class="category-node">

    <div class="category-node-name">
        <span>{{ $category->name }}</span>
        <div class="category-actions">
            <a href="{{ route('products.index', ['category_id' => $category->id]) }}" class="btn btn-primary">
                محصولات این دسته
            </a>
        </div>
    </div>

    @php($children = $category->children->where('type', $category->type))
    @if($children->isNotEmpty())

        <div class="category-node-children">

            @foreach($children as $child)

                @include('categories.partials.product-category-tree', [
                    'category' => $child
                ])

            @endforeach

        </div>

    @endif

</div>
