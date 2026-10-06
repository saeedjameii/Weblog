@extends('layout.panel')
@section('title', 'مدیریت کاربران — خبرنامه')

@section('header-actions')
    <a class="browse-link" href="{{ route('panel') }}">← بازگشت به صفحه قبل</a>
@endsection

@section('content')
<main class="page-area">
    <div class="main-content">
        <section class="intro">
            <p class="kicker">مدیریت سیستم</p>
            <h1 class="display-font">کاربران و نقش‌ها</h1>
            <p class="intro-copy">حساب‌های کاربری سایت را مدیریت کنید، ادمین‌های جدید بسازید و نقش‌ها را مشخص کنید.</p>
        </section>

        <div class="users-summary">
            <div class="summary-item">
                <span class="summary-label">کل کاربران</span>
                <strong>{{ $users->count() }}</strong>
            </div>
            <div class="summary-item">
                <span class="summary-label">کاربران فعال</span>
                <strong>{{ $users->whereNull('deleted_at')->count() }}</strong>
            </div>
            <div class="summary-item">
                <span class="summary-label">حساب‌های تعلیق‌شده</span>
                <strong>{{ $users->whereNotNull('deleted_at')->count() }}</strong>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success" style="margin-bottom: 24px;">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger" style="margin-bottom: 24px;">{{ session('error') }}</div>
        @endif

        <section aria-label="فهرست کاربران">
            
            @forelse ($users as $user)
                <article class="user-card {{ $user->trashed() ? 'is-deleted' : '' }}">
                    
                    <div class="user-profile-section">
                        <span class="user-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->first_name, 0, 1)) }}</span>
                        <div class="user-info">
                            <h3>
                                {{ $user->first_name }} {{ $user->last_name }}
                                <span class="status-badge {{ $user->trashed() ? 'deleted' : 'active' }}">
                                    {{ $user->trashed() ? 'تعلیق شده' : 'فعال' }}
                                </span>
                            </h3>
                            <p>{{ $user->email }}</p>
                            
                            <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                @if ($user->isCreator())
                                    <span class="tag" style="background:#e9ecef;">🌟 Creator (سازنده)</span>
                                @elseif ($user->isAdmin())
                                    <span class="tag" style="background:#e9ecef;">🛡️ Admin</span>
                                    @foreach ($user->roles as $role)
                                        <span class="tag">{{ $role->name }}</span>
                                    @endforeach
                                @else
                                    <span class="tag" style="background:#f8f9fa; border:1px solid var(--line);">👤 کاربر عادی</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="user-actions-section">
                        @if ($user->isCreator())
                            <span style="color:var(--muted); font-size:0.9rem; font-style:italic;">حساب سازنده قابل تغییر نیست</span>
                        @else

                            @if ($user->isAdmin() && !$user->trashed())
                                <form action="{{ route('panel.users.role.update', $user) }}" method="POST" class="role-form-group">
                                    @csrf
                                    @method('PUT')
                                    <label class="sr-only" for="roles-{{ $user->id }}">نقش‌های کاربر</label>
                                    <select id="roles-{{ $user->id }}" class="form-control role-select" name="role_ids[]" multiple>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" {{ $user->roles->contains($role->id) ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="button button-secondary" style="padding: 0 16px; white-space: nowrap;">ثبت نقش</button>
                                </form>
                            @endif

                            <div class="action-buttons-group">
                                
                                @if (auth('api')->user()->isCreator() && !$user->trashed())
                                    @if ($user->isAdmin())
                                        <form action="{{ route('panel.users.demote', $user) }}" method="POST" onsubmit="return confirm('آیا از تنزل این شخص به سطح کاربر عادی مطمئن هستید؟');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="button btn-outline">عزل از ادمین</button>
                                        </form>
                                    @else
                                        <form action="{{ route('panel.users.promote', $user) }}" method="POST" onsubmit="return confirm('آیا این کاربر به سطح ادمین ارتقا یابد؟');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="button button-primary">ارتقا به ادمین</button>
                                        </form>
                                    @endif
                                @endif

                                @can('permission', 'manage-users')
                                    @if ($user->trashed())
                                        <form action="{{ route('panel.users.restore', $user->id) }}" method="POST" onsubmit="return confirm('آیا می‌خواهید این کاربر را بازیابی کنید؟');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="button button-secondary">فعال‌سازی مجدد</button>
                                        </form>
                                    @else
                                        <a href="{{ route('panel.users.edit', $user) }}" class="button button-secondary">ویرایش کاربر</a>
                                        <form action="{{ route('panel.users.destroy', $user) }}" method="POST" onsubmit="return confirm('آیا از تعلیق این کاربر مطمئن هستید؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="button btn-outline-danger">تعلیق حساب</button>
                                        </form>
                                    @endif
                                @endcan
                                
                            </div>

                        @endif
                    </div>

                </article>
            @empty
                <div class="alert alert-info text-center" style="padding: 40px;">
                    <span style="font-size: 2rem; display: block; margin-bottom: 10px;">👥</span>
                    هنوز هیچ کاربری در سیستم ثبت نشده است.
                </div>
            @endforelse

        </section>
    </div>
</main>
@endsection