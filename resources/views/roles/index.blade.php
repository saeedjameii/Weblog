@extends('layout.master')

@section('header-actions')
    <a class="browse-link" href="{{ route('panel') }}">← بازگشت به صفحه قبل</a>
@endsection

@section('content')

<main class="page-area">
    <div class="main-content">

        {{-- Header --}}
        <section class="intro" style="max-width:100%;display:flex;justify-content:space-between;align-items:flex-end;gap:20px;flex-wrap:wrap;">
            <h1 class="display-font" style="font-size:clamp(2rem, 3.4vw, 3rem); margin:0;">نقش ها</h1>

            @can('permission', 'create-role')
                <a href="{{ route('roles.create') }}" class="button button-primary">
                    ساخت نقش
                </a>
            @endcan
        </section>


        {{-- Success Message --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif


        {{-- Roles --}}
        @forelse ($roles as $role)

            <div class="form-card" style="margin-top:24px;">

                {{-- Role Header --}}
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">

                    <div>
                        <h4 style="margin:0 0 4px;">
                            {{ $role->name }}
                        </h4>

                        @if ($role->description)
                            <p style="margin:0; color:var(--muted);">
                                {{ $role->description }}
                            </p>
                        @endif
                    </div>


                    {{-- Actions --}}
                    <div style="display:flex; gap:8px;">

                        @can('permission', 'update-role')
                            @if ($role->name !== 'creator')
                                <a
                                    href="{{ route('roles.edit', $role) }}"
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
                                    action="{{ route('roles.destroy', $role) }}"
                                    method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this role?');"
                                    style="display:inline;"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn text-danger" style="border:1px solid var(--line); background:none;">
                                        حذف
                                    </button>

                                </form>

                            @endif

                        @endcan

                    </div>

                </div>


                {{-- Permissions --}}
                <div style="margin-top:20px; border-top:1px solid var(--line); padding-top:16px;">

                    <h6 style="margin:0 0 12px; color:var(--muted);">
                        اختیارات
                    </h6>


                    @if ($role->permissions->count())

                        <div style="display:flex; flex-wrap:wrap; gap:8px;">

                            @foreach ($role->permissions as $permission)

                                <span class="permission-badge">
                                    {{ $permission->name }}
                                </span>

                            @endforeach

                        </div>

                    @else

                        <p style="color:var(--muted); margin:0;">
                            این نقش هیچ اختیاری ندارد
                        </p>

                    @endif

                </div>

            </div>

        @empty

            <div class="alert alert-info" style="margin-top:24px;">
                هیچ نقشی یافت نشد
            </div>

        @endforelse

    </div>
</main>

@endsection