
<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
</head>
<body>
    <p>Twój kod do logowania:</p>
    <p style="font-size: 24px; font-weight: 700; letter-spacing: 4px;">
        {{ $code }}
    </p>
    <p>Kod wygaśnie za {{ $expiresMinutes }} minut.</p>
    <p>Jeśli to nie Ty, zignoruj tę wiadomość.</p>
</body>
</html>
