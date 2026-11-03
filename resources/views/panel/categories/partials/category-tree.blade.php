<div class="category-node sortable-item" data-id="{{ $category->id }}">
    <div class="category-node-name">
        <div class="category-title-wrap">
            {{-- دستگیره کشیدن و رها کردن --}}
            <span class="drag-handle" title="برای تغییر اولویت کلیک کنید و بکشید">⋮⋮</span>
            <span>{{ $category->name }}</span>
        </div>

        <div class="category-actions">
            <a href="{{ route($category->type === 'post' ? 'posts.index' : 'products.index', ['category_id' => $category->id]) }}" class="btn btn-primary">
                {{ $category->type === 'post' ? 'پست‌های این دسته' : 'محصولات این دسته' }}
            </a>
            @can('permission', 'update-category')
                <a href="{{ route($category->type === 'post' ? 'panel.categories.posts.edit' : 'panel.categories.products.edit', $category->id) }}" class="btn btn-secondary">ویرایش</a>
            @endcan
            @can('permission', 'delete-category')
                <form action="{{ route('panel.categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('آیا از حذف این دسته‌بندی مطمئن هستید؟');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn text-danger">حذف</button>
                </form>
            @endcan
        </div>
    </div>

    @php($children = $category->children->where('type', $category->type))
    @if($children->isNotEmpty())
        {{-- لیست فرزندان برای مرتب‌سازی درون زیرمجموعه‌ها --}}
        <div class="category-node-children sortable-list" data-parent-id="{{ $category->id }}">
            @foreach($children as $child)
                @include('panel.categories.partials.category-tree', ['category' => $child])
            @endforeach
        </div>
    @endif
</div>
