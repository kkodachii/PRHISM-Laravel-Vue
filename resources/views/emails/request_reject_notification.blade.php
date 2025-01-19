<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supply Request Rejected</title>
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
            padding: 20px;
        }

        header img {
            width: 250px;
            height: auto;
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
            font-size: 1.5em;
            font-weight: bold;
        }

        .btn-container {
            text-align: center;
            margin: 30px 0;
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
            <img src="https://i.postimg.cc/CKNBJkWy/MUNICIPALITY-OF-PAOMBONG-4000-x-2000-px.png" alt="Paombong RHISM Logo">
        </header>

        <div class="content">
            <p class="greeting"><strong>Supply Request Rejected</strong></p>
            <p>We regret to inform you that your supply request has been rejected by <strong>{{ $barangay_name }}</strong>. This decision was made due to insufficient supplies available for your requested items.</p>
            
            <p>If you have any questions or would like further clarification regarding this decision, please do not hesitate to reach out to us.</p>

            <div class="btn-container">
                <a href="{{ $url }}" class="btn">View Request Details</a>
            </div>

            <p>We appreciate your understanding in this matter and thank you for your cooperation.</p>



        <footer>
            <p>&copy; Paombong Rural Health Inventory System Management {{ date('Y') }}</p>
        </footer>
    </div>
</body>

</html>
