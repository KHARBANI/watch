<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed</title>
    <link rel="stylesheet" href="../../CSS/index.css">
    <style>
        .failure-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            text-align: center;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .failure-container h1 {
            color: #FF0000;
            font-size: 2rem;
        }
        .failure-container p {
            font-size: 1.2rem;
            color: #555;
        }
        .failure-container a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 1rem;
            color: white;
            background-color: #FF0000;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .failure-container a:hover {
            background-color: #CC0000;
        }
    </style>
</head>
<body>
    <div class="failure-container">
        <h1>Payment Failed</h1>
        <p>Unfortunately, your payment could not be processed. Please try again or contact support.</p>
        <a href="product_details.php?watch_id=<?php echo htmlspecialchars($_GET['watch_id'] ?? ''); ?>">Try Again</a>
    </div>
</body>
</html>
