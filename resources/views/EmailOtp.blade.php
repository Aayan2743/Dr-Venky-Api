<!-- resources/views/emails/test.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email</title>
</head>
<body>
    <h1>Hello, {{ $data['name'] }}</h1>
    <p>Your OTP is: <strong>{{ $data['Otp'] }}</strong></p>
</body>
</html>
