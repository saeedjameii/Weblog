@extends('layout.master')
@section('title', 'مدیریت کاربران — خبرنامه')

@section('header-actions')
    <a class="browse-link" href="{{ route('panel') }}">← بازگشت به صفحه قبل</a>
@endsection

@section('content')
<main class="page-area admin-users-page">
    <div class="main-content">
        <section class="intro admin-users-intro">
            <p class="kicker">مدیریت سیستم</p>
            <h1 class="display-font">کاربران و نقش‌ها</h1>
            <p class="intro-copy">نقش هر کاربر را تعیین کنید و حساب‌های غیرفعال را از همین بخش مدیریت کنید.</p>
        </section>

        <div class="users-summary" aria-label="خلاصه کاربران">
            <div class="summary-item"><span class="summary-label">همه کاربران</span><strong>{{ $users->count() }}</strong></div>
            <div class="summary-item"><span class="summary-label">حساب‌های فعال</span><strong>{{ $users->whereNull('deleted_at')->count() }}</strong></div>
            <div class="summary-item"><span class="summary-label">حساب‌های غیرفعال</span><strong>{{ $users->whereNotNull('deleted_at')->count() }}</strong></div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <section class="users-card" aria-label="فهرست کاربران">
            <div class="users-card-heading">
                <div><h2>فهرست کاربران</h2><p>نقش‌ها را انتخاب کنید و تغییرات را برای هر کاربر ذخیره کنید.</p></div>
                <span class="users-count">{{ $users->count() }} کاربر</span>
            </div>

            <div class="users-list">
                @forelse ($users as $user)
                    <article class="user-row {{ $user->trashed() ? 'is-deleted' : '' }}">
                        <div class="user-profile">
                            <span class="user-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->first_name, 0, 1)) }}</span>
                            <div class="user-details">
                                <div class="user-name-line">
                                    <h3>{{ $user->first_name }} {{ $user->last_name }}</h3>
                                    <span class="account-status {{ $user->trashed() ? 'status-deleted' : 'status-active' }}">{{ $user->trashed() ? 'غیرفعال' : 'فعال' }}</span>
                                </div>
                                <p>{{ $user->email }}</p>
                                <div class="role-tags" aria-label="نقش‌های کاربر">
                                    @if ($user->isCreator())
                                        <span class="tag">creator</span>
                                    @else
                                        @forelse ($user->roles as $role)
                                            <span class="tag">{{ $role->name }}</span>
                                        @empty
                                            <span class="no-role">بدون نقش</span>
                                        @endforelse
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if ($user->isCreator())
                            <div class="creator-notice">حساب سازنده قابل تغییر نیست</div>
                        @else
                            <div class="user-actions">
                                <form action="{{ route('users.role.update', $user) }}" method="POST" class="role-form">
                                    @csrf
                                    @method('PUT')
                                    <label class="sr-only" for="roles-{{ $user->id }}">نقش‌های {{ $user->first_name }}</label>
                                    @if(!$user->trashed())
                                    <select id="roles-{{ $user->id }}" class="form-control role-select" name="role_ids[]" multiple>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" {{ $user->roles->contains($role->id) ? 'selected' : '' }}>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="button button-secondary">ذخیره نقش‌ها</button>
                                    @endif
                                </form>

                                @can('permission', 'manage-users')
                                    @if ($user->trashed())
                                        <form action="{{ route('users.restore', $user->id) }}" method="POST" onsubmit="return confirm('آیا می‌خواهید این کاربر را بازیابی کنید؟');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="button button-secondary">بازیابی کاربر</button>
                                        </form>
                                    @else
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('آیا از حذف این کاربر مطمئن هستید؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="button button-danger">حذف</button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        @endif
                    </article>
                @empty
                    <div class="users-empty"><span aria-hidden="true">👥</span><p>هنوز کاربری ثبت‌نام نکرده است.</p></div>
                @endforelse
            </div>
        </section>
    </div>
</main>
@endsection