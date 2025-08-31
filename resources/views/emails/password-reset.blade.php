<!DOCTYPE html>
<html>

<head>
    <title>Password Reset</title>
</head>

<body>
    <p>Hello,</p>
    <p>You are receiving this email because we received a password reset request for your account.</p>
    <p>Please click the following link to reset your password:</p>
    <p><a href="{{ url('/reset-password?token=' . $token . '&email=' . $email) }}">Reset Password</a></p>
    <p>If you did not request a password reset, no further action is required.</p>
    <p>Regards,</p>
    <p>Your Application Name</p>
</body>

</html>
