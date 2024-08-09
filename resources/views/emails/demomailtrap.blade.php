<!DOCTYPE html>
<html>

<head>
    <title>Verification Email</title>
    <style>
    /* Add some basic styling for the email */
    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        margin: 20px;
        padding: 0;
    }

    .container {
        max-width: 600px;
        margin: auto;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f9f9f9;
    }

    .header {
        background-color: #007bff;
        color: #fff;
        padding: 10px;
        text-align: center;
        border-radius: 5px 5px 0 0;
    }

    .content {
        margin: 20px 0;
    }

    .footer {
        font-size: 0.8em;
        color: #777;
        text-align: center;
        margin-top: 20px;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Verification Email</h1>
        </div>
        <div class="content">
            <p>Dear {{ $details['name'] }},</p>

            <p>Thank you for registering with [Your Company/Website Name]. To complete your sign-in process, please use
                the following verification code:</p>

            <p><strong>Verification Code: {{ $details['verificationCode'] }}</strong></p>

            <p>Please enter this code on the sign-in page to verify your email address. If you did not request this
                code, please disregard this email.</p>

            <p>If you have any questions or need assistance, feel free to contact our support team.</p>
        </div>
        <div class="footer">
            <p>Best regards,<br>[Company Name]</p>
        </div>
    </div>
</body>

</html>