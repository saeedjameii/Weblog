@extends('layout.master')

@section('content')
    <main>
      <div class="container">
        <ol class="breadcrumb" aria-label="مسیر صفحه">
          <li><a href="{{ route('home') }}">صفحه اصلی</a></li>
          <li aria-current="page">اخبار</li>
        </ol>

        <section class="sx-pagehead sx-pagehead--blog glass">
          <span class="sx-kicker sx-kicker--blog">آرشیو جامع</span>
          <h1 class="sx-title">آخرین اخبار و مقالات</h1>
          <p class="sx-lead">
            در این بخش می‌توانید تمامی اخبار، گزارش‌ها و مقالات منتشر شده توسط تحریریه را جستجو و مطالعه کنید.
          </p>
          <form class="search-form" method="GET" action="{{ route('posts.index') }}">
            <input
              class="search input"
              type="search"
              name="search"
              value="{{ request('search') }}"
              aria-label="جستجو در اخبار"
              placeholder="جستجو در متن یا تیتر اخبار..."/>
            <select class="select input" name="category_id" aria-label="دسته‌بندی" onchange="this.form.submit()">
              <option value="">همه دسته‌بندی‌ها</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->full_name }}</option>
                @endforeach
            </select>
            <button class="btn-glow btn-glow--blog search-submit" type="submit"><x-icon name="search" /> جست‌وجو</button>
          </form>
        </section>

        <div class="pgrid pgrid--posts" style="margin-block-start: var(--space);">
        @forelse ($posts as $post)
          <x-post-card :post="$post" />
        @empty
            <div class="sx-empty glass users-empty full-width">
                <x-icon name="empty" />
                <h2>خبری پیدا نشد</h2>
                <p>هیچ خبری برای نمایش وجود ندارد.</p>
            </div>
        @endforelse
        </div>

        {{ $posts->links('pagination.custom') }}
      </div>
    </main>
@endsection
