<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Login Details</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f6f6f6;">
    <div style="max-width: 600px; margin: auto; padding: 20px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
        <table width="100%" style="border-collapse: collapse;">
            <tr>
                <td style="text-align: center; padding: 20px;">
                    <h1 style="color: #333;">Welcome to Dr Venky Pets Clinic!</h1>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px; font-size: 16px; color: #666;">
                    <p>Hi <strong>{{ $name }}</strong>,</p>
                    <p>Your account has been successfully created. Here are your login details:</p>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px; font-size: 16px; color: #666;">
                    <table style="width: 100%; background-color: #f9f9f9; border-radius: 5px; padding: 10px;">
                        <tr>
                            <td style="padding: 8px 0;"><strong>Username:</strong></td>
                            <td style="padding: 8px 0;">{{ $mail }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0;"><strong>Password:</strong></td>
                            <td style="padding: 8px 0;">{{ $password }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="padding: 20px; text-align: center;">
                    <a href="{{env('APP_URL_DETAILS')}}" style="display: inline-block; padding: 12px 24px; font-size: 16px; color: #ffffff; background-color: #4CAF50; text-decoration: none; border-radius: 5px;">Log In to Your Account</a>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px; font-size: 14px; color: #999; text-align: center;">
                    <p>If you have any questions, please feel free to <a href="{{env('CONTACT_MAIL')}}" style="color: #4CAF50;">contact our support team</a>.</p>
                </td>
            </tr>
            <tr>
                <td style="padding: 20px; font-size: 14px; color: #999; text-align: center;">
                    <p>Thank you,<br>The Dr Venky Pets Clinic! Team</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
