<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('messages.admin_invitation_subject') }}</title>
</head>
<body style="
    margin:0;
    padding:20px;
    background-color:#f4f4f4;
    color:#222;
    font-family: Arial, Helvetica, sans-serif;
">
<div style="
    max-width:600px;
    margin:0 auto;
    background:#ffffff;
    padding:24px;
    border-radius:6px;
">
    <p>{{ __('messages.admin_invited_greeting') }}</p>
    <p>{{ __('messages.admin_invited_text') }}</p>
    <p style="margin:24px 0;">
        <a href="{{ $registerUrl }}"
           style="
               display:inline-block;
               padding:10px 16px;
               background:#007bff;
               color:#ffffff;
               text-decoration:none;
               border-radius:4px;
               font-weight:bold;
           ">
            {{ __('messages.accept_invitation') }}
        </a>
    </p>
    <p>
        {{ __('messages.invitation_expires') }}:
        <strong>{{ $invitation->expires_at->format('Y-m-d H:i') }}</strong>
    </p>
    <hr style="margin:24px 0; border:none; border-top:1px solid #ddd;">
    <p style="font-size:12px; color:#666;">
        {{ __('messages.ignore_if_not_you') }}
    </p>
</div>
</body>
</html>
