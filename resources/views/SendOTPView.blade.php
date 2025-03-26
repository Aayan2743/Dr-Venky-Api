<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Send OTP For Password Reset</title>
</head>
<body>
    
    <h1>Hello {{ $name }},</h1>
    <p>Your OTP code is: <strong>{{ $otp }}</strong></p>
    <p>Please use this code to verify your identity.</p>
</body>
</html>