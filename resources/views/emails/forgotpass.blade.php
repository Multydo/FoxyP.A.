<!DOCTYPE html>
<html>

<head>
    <title>Password Reset Code</title>
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
            <h1>Password Reset Code</h1>
        </div>
        <div class="content">
            <p>Dear {{ $details['fname'] }},</p>

            <p>We received a request to reset your password for your account. To complete the process, please use the
                following code:</p>

            <p><strong>Reset Code: {{ $details['resetCode'] }}</strong></p>

            <p>Please enter this code on the password reset page to proceed. If you did not request a password reset,
                please ignore this email.</p>


        </div>
        <div class="footer">
            <p>Best regards,<br>Foxy P.A.</p>
        </div>
    </div>
</body>

</html>