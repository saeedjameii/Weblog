@extends('layout.master')

@section('title', 'Create Roles - ToolShare')

@section('header-actions')
    <a class="browse-link" href="{{ route('roles.index') }}">← بازگشت به صفحه قبل</a>
@endsection

@section('content')

<main class="page-area">
    <div class="main-content">

        <section class="intro">
            <p class="kicker">مدیریت سیستم</p>
            <h1 class="display-font">ساخت نقش</h1>
        </section>

        <div class="workspace">
            <form action="{{ route('roles.store') }}" method="POST" class="form-card">
                @csrf

                <div class="field-grid">

                    <div class="field full-field">
                        <label>نام نقش</label>
                        <input class="form-control" type="text" name="name">
                    </div>

                    <div class="field full-field">
                        <label>اختیارات</label>

                        @foreach ($permissions as $permission)
                            <div class="permission-checkbox-row">
                                <input
                                    type="checkbox"
                                    name="permissions[]"
                                    value="{{ $permission->id }}"
                                >

                                <label style="margin:0;">
                                    {{ $permission->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>

                </div>

                <button type="submit" class="button button-primary" style="margin-top:8px;">
                    ساخت نفش
                </button>
            </form>
        </div>

    </div>
</main>

@endsection