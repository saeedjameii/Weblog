<div class="category-node">
    <div class="category-node-name">
        <span>{{ $category->name }}</span>
        <div class="category-actions">
            <a href="{{ route($category->type === 'post' ? 'posts.index' : 'products.index', ['category_id' => $category->id]) }}" class="btn btn-primary">
                {{ $category->type === 'post' ? 'پست‌های این دسته' : 'محصولات این دسته' }}
            </a>
            @can('permission', 'update-category')
                <a href="{{ route($category->type === 'post' ? 'panel.categories.posts.edit' : 'panel.categories.products.edit', $category->id) }}" class="btn btn-secondary">ویرایش</a>
            @endcan
            @can('permission', 'delete-category')
                <form action="{{ route('panel.categories.destroy', $category->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn text-danger">حذف</button>
                </form>
            @endcan
        </div>
    </div>

    @php($children = $category->children->where('type', $category->type))
    @if($children->isNotEmpty())
        <div class="category-node-children">
            @foreach($children as $child)
                @include('panel.categories.partials.category-tree', ['category' => $child])
            @endforeach
        </div>
    @endif
</div>
