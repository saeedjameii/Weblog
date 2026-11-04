<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>در حال به‌روزرسانی</title>
    @vite(['resources/css/style.css', 'resources/css/theme.css'])
</head>
<body class="site-theme">
    <main style="min-block-size:100dvh;display:grid;place-items:center;padding:1.25rem;">
        <section class="glass" style="max-inline-size:520px;inline-size:100%;padding:clamp(1.5rem,6vw,3rem);text-align:center;">
            <p style="font-size:clamp(3rem,14vw,5rem);font-weight:700;line-height:1;margin:0;background:linear-gradient(135deg,var(--shop-500),var(--accent-500));-webkit-background-clip:text;background-clip:text;color:transparent;">503</p>
            <h1 style="font-size:clamp(1.2rem,4vw,1.6rem);margin:.75rem 0 .5rem;">در حال به‌روزرسانی</h1>
            <p style="color:var(--t-text-soft);margin:0 0 1.5rem;">سایت در حال به‌روزرسانی است و به زودی برمی‌گردد. از صبوری شما سپاسگزاریم.</p>
            <a class="btn-soft" href="{{ url()->current() }}">بارگذاری دوباره</a>
        </section>
    </main>
</body>
</html>
