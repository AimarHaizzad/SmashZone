<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<style>
@media only screen and (max-width: 600px) {
.inner-body {
width: 100% !important;
}

.footer {
width: 100% !important;
}
}

@media only screen and (max-width: 500px) {
.button {
width: 100% !important;
}
}
</style>
</head>
<body>

<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="center">
<table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
{{ $header ?? '' }}

<!-- Email Body -->
<tr>
<td class="body" width="100%" cellpadding="0" cellspacing="0" style="border: hidden !important;">
<table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<!-- Body content -->
<tr>
<td class="content-cell">
<div style="font-family: Arial, sans-serif; max-width: 570px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
    <!-- Header -->
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center;">
        <h1 style="color: white; margin: 0; font-size: 28px; font-weight: bold;">🏸 SmashZone</h1>
        <p style="color: rgba(255, 255, 255, 0.9); margin: 10px 0 0 0; font-size: 16px;">Your Premier Badminton Court Booking Platform</p>
    </div>

    <!-- Content -->
    <div style="padding: 40px 30px;">
        {{ Illuminate\Mail\Markdown::parse($slot) }}
    </div>

    <!-- Footer -->
    <div style="background-color: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #e9ecef;">
        <p style="color: #6c757d; margin: 0 0 15px 0; font-size: 14px;">
            © {{ date('Y') }} SmashZone. All rights reserved.
        </p>
        <p style="color: #6c757d; margin: 0; font-size: 12px;">
            This email was sent to you because you have an account with SmashZone.
        </p>
        <div style="margin-top: 20px;">
            <a href="{{ url('/') }}" style="color: #667eea; text-decoration: none; margin: 0 10px; font-size: 14px;">Home</a>
            <a href="{{ url('/courts') }}" style="color: #667eea; text-decoration: none; margin: 0 10px; font-size: 14px;">Book Courts</a>
            <a href="{{ url('/contact') }}" style="color: #667eea; text-decoration: none; margin: 0 10px; font-size: 14px;">Contact</a>
        </div>
    </div>
</div>
</td>
</tr>
</table>
</td>
</tr>

{{ $footer ?? '' }}
</table>
</td>
</tr>
</table>
</body>
</html>
