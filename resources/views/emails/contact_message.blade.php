<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>New contact message</title>
</head>

<body>

    <h2>New contact message</h2>

    <p>
        <strong>Name:</strong><br>
        {{ $data['name'] }}
    </p>

    <p>
        <strong>Email:</strong><br>
        {{ $data['email'] }}
    </p>

    <p>
        <strong>Message:</strong><br>
        {!! nl2br(e($data['message'])) !!}
    </p>

</body>

</html>