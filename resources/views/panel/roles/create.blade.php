@extends('layout.panel')

@section('title', 'ساخت نقش - خبرنامه')

@section('header-actions')
    <a class="browse-link" href="{{ route('panel.roles.index') }}">← بازگشت به صفحه قبل</a>
@endsection

@section('content')

<main class="page-area">
    <div class="main-content">

        <section class="intro">
            <p class="kicker">مدیریت سیستم</p>
            <h1 class="display-font">ساخت نقش</h1>
        </section>

        <div class="workspace">
            <form action="{{ route('panel.roles.store') }}" method="POST" class="form-card">
                @csrf

                <div class="field-grid">

                    <div class="field full-field">
                        <label>نام نقش</label>
                        <input class="form-control" type="text" name="name">
                    </div>

                    <div class="field full-field">
                        <label>اختیارات دسترسی</label>

                        <div class="permission-checkbox-grid">
                            @foreach ($permissions as $permission)
                                <label class="permission-checkbox-row">
                                    <input
                                        type="checkbox"
                                        name="permissions[]"
                                        value="{{ $permission->id }}"
                                    >
                                    <span class="permission-label">
                                        {{ $permission->name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                </div>

                <div class="actions">
                    <a class="button button-secondary" href="{{ route('panel.roles.index') }}">انصراف</a>
                    <button type="submit" class="button button-primary">
                       ساخت نقش
                    </button>
                </div>
            </form>
        </div>

    </div>
</main>

@endsection