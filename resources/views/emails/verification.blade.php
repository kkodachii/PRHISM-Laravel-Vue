<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to PRHISM!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            color: #333;
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        header {
            background-image: url('https://i.postimg.cc/VN0D9FyV/photo-2024-08-14-10-24-39.png');
            text-align: center;
        }

        header img {
            width: 250px;
            height: auto;
        }

        header h1 {
            color: black !important; 
        }

        .content {
            padding: 20px;
            text-align: left;
        }

        .content p {
            line-height: 1.6;
            margin-bottom: 15px;
            color: #333; 
        }

        .greeting {
            font-size: 1.5em; /* Make the text larger */
            font-weight: bold;
        }

        .btn-container {
            text-align: center; /* Center the button */
            margin: 20px 0; /* Optional spacing around the button */
        }

        .btn {
            display: inline-block;
            background-color: #007bff; 
            color: white !important; 
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3 !important; 
        }

        footer {
            background-color: #f1f1f1; 
            color: #666; 
            text-align: center;
            padding: 15px;
            font-size: 12px;
        }

        footer p {
            margin: 5px 0;
        }

        footer a {
            color: #007bff; 
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <header>
            <img src="https://i.postimg.cc/CMf5yj1h/logos.png" alt="Test Company Logo">
        </header>

        <div class="content">
        <p class="greeting"><strong>Hello {{ $user->name }},</strong></p>
            <p>Thank you for registering with PRHISM. Please confirm your email address by clicking the button below.</p>
            <div class="btn-container">
            <a href="{{ $url }}" class="btn">Verify Your Email Address</a>
            </div>
            <p>Once your email is verified, you'll be able to log in to your account.</p>
            <p>If you have any questions, feel free to contact us at <a href="mailto:rhupaombonghosting@gmail.com">rhupaombonghosting@gmail.com</a>.</p>
        </div>

        <footer>
            <p>&copy; Paombong Rural Health Inventory System Management {{ date('Y') }}</p>
        </footer>
    </div>
</body>

</html>
