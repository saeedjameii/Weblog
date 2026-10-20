@extends('layout.panel')

@section('title', 'ویرایش نقش - خبرنامه')

@section('header-actions')
    <a class="browse-link" href="{{ route('panel.roles.index') }}">← بازگشت به صفحه قبل</a>
@endsection

@section('content')

<main class="page-area">
    <div class="main-content">

        <section class="intro">
            <p class="kicker">مدیریت سیستم</p>
            <h1 class="display-font">ویرایش نقش</h1>
        </section>

        <div class="workspace">
            <form action="{{ route('panel.roles.update', $role) }}" method="POST" class="form-card">

                @csrf
                @method('PUT')

                <div class="field-grid">

                    <div class="field full-field">

                        <label for="name">
                            نام نقش
                        </label>

                        <input
                            class="form-control"
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $role->name) }}"
                        >

                        @error('name')
                            <span class="validation-message">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    <div class="field full-field">

                        <label>اختیارات</label>

                        @foreach ($permissions as $permission)

                            <div class="permission-checkbox-row">

                                <input
                                    type="checkbox"
                                    name="permissions[]"
                                    value="{{ $permission->id }}"
                                    @checked(in_array($permission->id, $rolePermissions))
                                >

                                <label class="permission-label">
                                    {{ $permission->name }}
                                </label>

                            </div>

                        @endforeach


                        @error('permissions')
                            <span class="validation-message">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>

                </div>

                <button type="submit" class="button button-primary mt-8">
                    بروزرسانی نقش
                </button>

            </form>
        </div>

    </div>
</main>

@endsection