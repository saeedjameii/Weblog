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
        <section class="intro" style="max-width:100%;display:flex;justify-content:space-between;align-items:flex-end;gap:20px;flex-wrap:wrap;">
            <div>
                <h1 class="display-font" style="font-size:clamp(2rem, 3.4vw, 3rem); margin:8px 0 0;">دسته‌بندی‌ها</h1>
           </div>
            @can('permission', 'create-category')
                <a href="{{ route('categories.create') }}" class="button button-primary">
                    + ساخت دسته‌بندی جدید
                </a>
            @endcan
        </section>


        {{-- Categories Card --}}
        <div class="form-card" style="margin-top:34px;">

            <div class="section-heading" style="justify-content:space-between; align-items:center; margin-bottom:0;">
                <div>
                    <h2 style="margin:0;">لیست دسته‌بندی‌ها</h2>
                    <p class="section-description" style="margin:4px 0 0;">{{ $categories->count() }} دسته‌بندی‌ها</p>
                </div>
            </div>

            @if ($categories->count())

                <div style="margin-top:24px;">

                    @foreach ($categories as $category)

                        <div class="spec-row" style="align-items:flex-start;">

                            {{-- Title --}}
                            <div style="display:flex; align-items:flex-start; gap:14px;">
                                <span class="step-number" style="background:#eef4eb; color:var(--forest); font-size:18px; flex-shrink:0;">📁</span>

                                    @include('categories.partials.category-tree', [
                                        'category' => $category
                                    ])
                            </div>
                        </div>

                    @endforeach

                </div>

            @else

                {{-- Empty State --}}
                <div style="text-align:center; padding:60px 20px;">

                    <div style="font-size:45px;">📁</div>

                    <h3 style="margin:18px 0 8px;">هیچ دسته‌بندی‌ای وجود ندارد</h3>

                    <p style="color:var(--muted);">شما هنوز هیچ دسته‌بندی‌ای ایجاد نکرده‌اید.</p>

                    <a href="{{ route('categories.create') }}" class="button button-primary" style="margin-top:14px;">
                        + ساخت دسته‌بندی جدید
                    </a>

                </div>

            @endif

        </div>

    </div>
</main>

@endsection