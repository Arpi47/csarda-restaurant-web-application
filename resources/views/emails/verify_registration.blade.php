<!DOCTYPE html>
<html lang="{{ $user->language ?? 'en' }}">

<head>
    <meta charset="UTF-8">
</head>

<body
    style="
    margin:0;
    padding:40px;
    background:#f3efe8;
    font-family:Arial,Helvetica,sans-serif;
    color:#333;
">
    <div
        style="
    max-width:650px;
    margin:auto;
    background:#ffffff;
    border-radius:16px;
    overflow:hidden;
    box-shadow:0 8px 25px rgba(0,0,0,.08);
">
        <div
            style="
        background:#5b3a29;
        color:white;
        padding:35px;
        text-align:center;
    ">
            <h1 style="
            margin:0;
            font-size:34px;
        ">
                🍽️ Csárda
            </h1>
            <p style="
            margin-top:10px;
            opacity:.9;
            font-size:15px;
        ">
                {{ __('messages.restaurant_tagline') }}
            </p>
        </div>
        <div style="
        padding:40px;
    ">
            <h2 style="
            color:#5b3a29;
        ">
                {{ __('messages.hello') }}
                {{ $user->first_name }}
            </h2>
            <p>
                {{ __('messages.registration_approved_body') }}
            </p>
            <div style="
            margin:35px 0;
            text-align:center;
        ">
                <a href="{{ $url }}"
                    style="
                    background:#5b3a29;
                    color:white;
                    padding:15px 35px;
                    border-radius:999px;
                    text-decoration:none;
                    font-weight:bold;
                    display:inline-block;
                ">
                    {{ __('messages.verify_email') }}
                </a>
            </div>
            <p>
                {{ __('messages.thank_you') }},
                <br>
                <strong>
                    Csárda
                </strong>
            </p>
        </div>
        <div
            style="
        background:#5b3a29;
        color:#ddd;
        text-align:center;
        padding:18px;
        font-size:13px;
    ">
            © {{ date('Y') }} Csárda
        </div>
    </div>
</body>

</html>
