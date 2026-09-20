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
            <h1 class="display-font">ویرایش نقش</h1>
        </section>

        <div class="workspace">
            <form action="{{ route('roles.update', $role) }}" method="POST" class="form-card">

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
                            <span class="validation-message" style="display:block;">
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

                                <label style="margin:0;">
                                    {{ $permission->name }}
                                </label>

                            </div>

                        @endforeach


                        @error('permissions')
                            <span class="validation-message" style="display:block;">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>

                </div>

                <button type="submit" class="button button-primary" style="margin-top:8px;">
                    بروزرسانی نقش
                </button>

            </form>
        </div>

    </div>
</main>

@endsection