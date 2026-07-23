<!DOCTYPE html>
<html lang="{{ $reservation->language ?? 'en' }}">

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
            <h1 style="margin:0;font-size:34px;">
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
        <div style="padding:40px;">
            @if ($reservation->status === 'approved')
                <h2 style="color:#5b3a29;">
                    {{ __('messages.reservation_approved_subject') }}
                </h2>
                <p>
                    {{ __('messages.email_greeting', ['name' => $reservation->fname]) }}
                </p>
                <p>
                    {{ __('messages.reservation_confirmed_intro') }}
                </p>
            @else
                <h2 style="color:#b22222;">
                    {{ __('messages.reservation_rejected_subject') }}
                </h2>
                <p>
                    {{ __('messages.email_greeting', ['name' => $reservation->fname]) }}
                </p>
                <p>
                    {{ __('messages.reservation_rejected_intro') }}
                </p>
            @endif
            <div
                style="
            margin:35px 0;
            background:#faf8f4;
            border-left:5px solid #b68b45;
            padding:20px;
        ">
                <table width="100%" cellpadding="8">
                    <tr>
                        <td><strong>{{ __('messages.date') }}</strong></td>
                        <td>{{ $formattedDate }}</td>
                    </tr>

                    <tr>
                        <td><strong>{{ __('messages.time') }}</strong></td>
                        <td>{{ $formattedTime }}</td>
                    </tr>

                    <tr>
                        <td><strong>{{ __('messages.guests') }}</strong></td>
                        <td>{{ $reservation->guests }}</td>
                    </tr>
                </table>
            </div>
            @if ($reservation->status === 'approved')
                <p>
                    {{ __('messages.we_look_forward') }}
                </p>
            @else
                <p>
                    {{ __('messages.try_another_date') }}
                </p>
            @endif
            <br>
            <p>
                {{ __('messages.best_regards') }}<br>
                <strong>Csárda</strong>
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
