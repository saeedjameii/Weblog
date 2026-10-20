@extends('layout.panel')

@section('header-actions')
    <a class="browse-link" href="{{ route('panel') }}">← بازگشت به صفحه قبل</a>
@endsection

@section('content')

<main class="page-area">
    <div class="main-content">
        @if (session()->has('error'))
            <div class="text-danger">{{ session('error') }}</div>
        @endif
        @if (session()->has('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Header --}}
        <section class="intro intro--header">
            <h1 class="display-font panel-title--roles">نقش ها</h1>

            @can('permission', 'create-role')
                <a href="{{ route('panel.roles.create') }}" class="button button-primary">
                    ساخت نقش
                </a>
            @endcan
        </section>


        {{-- Roles --}}
        @forelse ($roles as $role)

            <div class="form-card role-card">

                {{-- Role Header --}}
                <div class="role-header">

                    <div>
                        <h4 class="role-name">
                            {{ $role->name }}
                        </h4>

                        @if ($role->description)
                            <p class="role-description">
                                {{ $role->description }}
                            </p>
                        @endif
                    </div>


                    {{-- Actions --}}
                    <div class="role-actions">

                        @can('permission', 'update-role')
                            @if ($role->name !== 'creator')
                                <a
                                    href="{{ route('panel.roles.edit', $role) }}"
                                    class="btn btn-secondary"
                                >
                                    ویرایش
                                </a>
                            @endif
                        @endcan


                        {{-- Creator cannot be deleted --}}
                        @can('permission', 'delete-role')

                            @if ($role->name !== 'creator')

                                <form
                                    action="{{ route('panel.roles.destroy', $role) }}"
                                    method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this role?');"
                                    class="d-inline"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn text-danger role-delete-btn">
                                        حذف
                                    </button>

                                </form>

                            @endif

                        @endcan

                    </div>

                </div>


                {{-- Permissions --}}
                <div class="role-permissions">

                    <h6 class="role-permissions-title">
                        اختیارات
                    </h6>


                    @if ($role->permissions->count())

                        <div class="permissions-list">

                            @foreach ($role->permissions as $permission)

                                <span class="permission-badge">
                                    {{ $permission->name }}
                                </span>

                            @endforeach

                        </div>

                    @else

                        <p class="color-muted m-0">
                            این نقش هیچ اختیاری ندارد
                        </p>

                    @endif

                </div>

            </div>

        @empty

            <div class="alert alert-info mt-24">
                هیچ نقشی یافت نشد
            </div>

        @endforelse

    </div>
</main>

@endsection