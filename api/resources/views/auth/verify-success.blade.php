<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Successful</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 100px;
            background-color: #f8fafc;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: auto;
        }
        h1 {
            color: #10b981;
        }
        p {
            margin-top: 20px;
        }
    </style>
    <script>
        setTimeout(function() {
            window.location.href = "{{ config('app.web.base_url') }}/login";
        }, 5000);
    </script>
</head>
<body>
    <div class="container">
        <h1>Email Verified Successfully!</h1>
        <p>You will be redirected to the login page in 5 seconds...</p>
    </div>
</body>
</html>
