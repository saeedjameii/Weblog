<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>بازیابی رمز عبور</title>
</head>
<body style="margin:0;background:#f3f4f6;color:#1f2937;font-family:Tahoma,Arial,sans-serif;direction:rtl">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f3f4f6;padding:32px 12px">
        <tr><td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background:#fff;border-radius:16px;overflow:hidden">
                <tr><td style="background:#0f766e;padding:28px 32px;text-align:center;color:#fff">
                    <div style="font-size:13px;opacity:.85">چی بهتره؟</div>
                    <h1 style="margin:10px 0 0;font-size:22px">بازیابی رمز عبور</h1>
                </td></tr>
                <tr><td style="padding:32px;line-height:2;text-align:right">
                    <p style="margin:0 0 12px">سلام {{ $user->first_name }} عزیز،</p>
                    <p style="margin:0 0 24px;color:#4b5563">برای انتخاب رمز عبور جدید، دکمه زیر را بزنید. این لینک تا ۶۰ دقیقه اعتبار دارد.</p>
                    <p style="margin:0 0 24px;text-align:center">
                        <a href="{{ $url }}" style="display:inline-block;padding:12px 28px;background:#0f766e;color:#fff;text-decoration:none;border-radius:9px;font-weight:bold">تغییر رمز عبور</a>
                    </p>
                    <p style="margin:0 0 8px;color:#6b7280;font-size:13px">اگر دکمه کار نکرد، این نشانی را در مرورگر باز کنید:</p>
                    <p dir="ltr" style="margin:0;word-break:break-all;font-size:12px;color:#0f766e"><a href="{{ $url }}" style="color:#0f766e">{{ $url }}</a></p>
                    <p style="margin:24px 0 0;color:#6b7280;font-size:13px">اگر درخواست بازیابی رمز را نداده‌اید، این ایمیل را نادیده بگیرید.</p>
                </td></tr>
                <tr><td style="padding:16px 32px;background:#f9fafb;color:#9ca3af;font-size:12px;text-align:center">این پیام به‌صورت خودکار ارسال شده است.</td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
