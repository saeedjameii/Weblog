@extends('layout.master')

@section('title', 'دسته‌بندی‌ها — خبرنامه')

@section('content')

<main class="page-area">
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif  

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="main-content">

        {{-- Page Header --}}
        <section class="intro intro--header">
            <div>
                <h1 class="display-font panel-title--categories">دسته‌بندی‌ها</h1>
           </div>
            @can('permission', 'create-category')
                <a href="{{ route('panel.categories.create') }}" class="button button-primary">
                    + ساخت دسته‌بندی جدید
                </a>
            @endcan
        </section>


        {{-- Categories Card --}}
        <div class="form-card categories-card">

            <div class="section-heading section-heading--flush">
                <div>
                    <h2 class="m-0">لیست دسته‌بندی‌ها</h2>
                    <p class="section-description section-description--flush">{{ $categories->count() }} دسته‌بندی‌ها</p>
                </div>
            </div>

            @if ($categories->count())

                <div class="categories-list">

                    @foreach ($categories as $category)

                        <div class="spec-row spec-row--top">

                            {{-- Title --}}
                            <div class="category-icon-wrap">
                                <span class="step-number step-number--category">📁</span>

                                    @include('categories.partials.category-tree', [
                                        'category' => $category
                                    ])
                            </div>
                        </div>

                    @endforeach

                </div>

            @else

                {{-- Empty State --}}
                <div class="empty-state">

                    <div class="empty-state-icon">📁</div>

                    <h3 class="empty-state-title">هیچ دسته‌بندی‌ای وجود ندارد</h3>

                    <p class="color-muted"> هنوز هیچ دسته‌بندی‌ای ایجاد نشده است  .</p>


                </div>

            @endif

        </div>

    </div>
</main>

@endsection