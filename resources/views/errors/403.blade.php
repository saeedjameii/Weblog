@php
    $rawMessage = isset($exception) ? trim((string) $exception->getMessage()) : '';
    $hasPersianMessage = $rawMessage !== '' && preg_match('/[\x{0600}-\x{06FF}]/u', $rawMessage);
    $detail = $hasPersianMessage ? $rawMessage : 'شما اجازه‌ی دسترسی به این صفحه را ندارید.';
@endphp
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>دسترسی غیرمجاز</title>
    @vite(['resources/css/style.css', 'resources/css/theme.css'])
</head>
<body class="site-theme">
    <main style="min-block-size:100dvh;display:grid;place-items:center;padding:1.25rem;">
        <section class="glass" style="max-inline-size:520px;inline-size:100%;padding:clamp(1.5rem,6vw,3rem);text-align:center;">
            <p style="font-size:clamp(3rem,14vw,5rem);font-weight:700;line-height:1;margin:0;background:linear-gradient(135deg,var(--shop-500),var(--accent-500));-webkit-background-clip:text;background-clip:text;color:transparent;">403</p>
            <h1 style="font-size:clamp(1.2rem,4vw,1.6rem);margin:.75rem 0 .5rem;">دسترسی غیرمجاز</h1>
            <p style="color:var(--t-text-soft);margin:0 0 1.5rem;">{{ $detail }}</p>
            <div style="display:flex;flex-wrap:wrap;gap:.75rem;justify-content:center;">
                <a class="btn-glow" href="{{ url('/') }}">بازگشت به صفحه اصلی</a>
                <a class="btn-soft" href="{{ url()->previous() }}">صفحه قبلی</a>
                @guest('api')
                    <a class="btn-soft" href="{{ route('login') }}">ورود به حساب</a>
                @endguest
            </div>
        </section>
    </main>
</body>
</html>