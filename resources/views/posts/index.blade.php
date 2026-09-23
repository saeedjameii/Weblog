@extends('layout.master')

@section('content')
    <main>
      <section class="browse-header">
        <div class="container">
          <div class="kicker">آرشیو جامع</div>
          <h1 class="section-title">آخرین اخبار و مقالات</h1>
          <p style="color: var(--muted); max-width: 650px; line-height: 1.7">
            در این بخش می‌توانید تمامی اخبار، گزارش‌ها و مقالات منتشر شده توسط تحریریه را جستجو و مطالعه کنید.
          </p>
          <form class="search-box" method="GET" action="{{ route('posts.index') }}">
            <input
              class="search"
              type="search"
              name="search"
              value="{{ request('search') }}"
              placeholder="جستجو در متن یا تیتر اخبار..."/>
            <select class="select" name="category_id" onchange="this.form.submit()">
              <option value="">همه دسته‌بندی‌ها</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            <button class="button button-primary search-submit" type="submit">
                جست‌وجو
            </button>
          </form>
        </div>
      </section>
      <section>
        <div class="container news-grid">
        @forelse ($posts as $post)
          <article class="news-card">
            <div class="news-body">
              <div style="display: flex; gap: 5px; flex-wrap: wrap; margin-bottom: 10px;">
                  @forelse ($post->categories as $category)
                      <span class="tag">{{ $category->name }}</span>
                  @empty
                      <span class="tag">عمومی</span>
                  @endforelse
              </div>
              <h3><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a></h3>
              <p class="news-excerpt">{{ Str::limit($post->description, 120) }}</p>
              <div class="card-bottom">
                <span class="author">✍️ {{ $post->user->first_name }} {{ $post->user->last_name }}</span>
                <a class="read-more" href="{{ route('posts.show', $post) }}">ادامه مطلب ←</a>
              </div>
            </div>
          </article>
        @empty
            <div class="users-empty" style="grid-column: 1 / -1;">
                <p>هیچ خبری برای نمایش وجود ندارد.</p>
            </div>
        @endforelse
        </div>

        <div class="container">
            {{ $posts->links('pagination.custom') }}
        </div>
      </section>
    </main>
@endsection
