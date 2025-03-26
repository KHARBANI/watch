<?php
session_start();

// Check if payment ID and watch ID are provided
if (isset($_GET['payment_id']) && isset($_GET['watch_id'])) {
    $payment_id = $_GET['payment_id'];
    $watch_id = $_GET['watch_id'];

    // Database connection
    $conn = new mysqli("localhost", "root", "", "watch_store");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Verify payment using Razorpay API
        $api_key = "rzp_test_QoxUlzfLT9H8al"; // Razorpay test key ID
        $api_secret = "m3fl5eP9tZH23fFtkjAoHEyV"; // Razorpay key secret

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.razorpay.com/v1/payments/$payment_id");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $api_key . ":" . $api_secret);

        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_status !== 200) {
            throw new Exception("Payment verification failed. Please contact support.");
        }

        $payment_data = json_decode($response, true);
        if ($payment_data['status'] !== 'captured') {
            throw new Exception("Payment not captured. Payment status: " . $payment_data['status']);
        }

        // Fetch product details
        $product_sql = "SELECT Price, Dealer_ID FROM product_table WHERE Watch_ID = ?";
        $product_stmt = $conn->prepare($product_sql);
        $product_stmt->bind_param("i", $watch_id);
        $product_stmt->execute();
        $product_result = $product_stmt->get_result();
        if ($product_result->num_rows === 0) {
            throw new Exception("Product not found.");
        }
        $product = $product_result->fetch_assoc();
        $unit_price = $product['Price'];
        $dealer_id = $product['Dealer_ID'];

        // Insert into order_table
        $customer_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $shipping_address_id = 1; // Replace with actual shipping address ID
        $order_status = "Confirmed";
        $total_price = $unit_price; // Total price is the unit price for a single item
        $order_sql = "INSERT INTO order_table (Customer_ID, Order_Date, Order_Time, Total_Price, Shipping_Address_ID, Order_Status) 
                      VALUES (?, CURDATE(), CURTIME(), ?, ?, ?)";
        $order_stmt = $conn->prepare($order_sql);
        $order_stmt->bind_param("idss", $customer_id, $total_price, $shipping_address_id, $order_status);
        $order_stmt->execute();
        $order_id = $conn->insert_id;

        // Insert into order_detail_table
        $quantity = 1; // Assuming 1 unit per order
        $subtotal = $quantity * $unit_price;
        $order_detail_sql = "INSERT INTO order_detail_table (Watch_ID, Order_ID, Quantity, Unit_Price, Subtotal) 
                             VALUES (?, ?, ?, ?, ?)";
        $order_detail_stmt = $conn->prepare($order_detail_sql);
        $order_detail_stmt->bind_param("iiidd", $watch_id, $order_id, $quantity, $unit_price, $subtotal);
        $order_detail_stmt->execute();

        // Insert into payment_table
        $payment_method = "Razorpay"; // Replace with actual payment method
        $payment_sql = "INSERT INTO payment_table (Payment_ID, Order_ID, Payment_Method, Amount, Payment_Date, Payment_Time) 
                        VALUES (?, ?, ?, ?, CURDATE(), CURTIME())";
        $payment_stmt = $conn->prepare($payment_sql);
        $payment_stmt->bind_param("sisd", $payment_id, $order_id, $payment_method, $total_price);
        $payment_stmt->execute();

        // Update stock quantity in product_table
        $update_stock_sql = "UPDATE product_table SET Stock_Quantity = Stock_Quantity - ? WHERE Watch_ID = ?";
        $update_stock_stmt = $conn->prepare($update_stock_sql);
        $update_stock_stmt->bind_param("ii", $quantity, $watch_id);
        $update_stock_stmt->execute();

        // Commit transaction
        $conn->commit();
        $message = "Your order has been placed successfully!";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $message = "Payment failed: " . $e->getMessage();
    }

    $product_stmt->close();
    $order_stmt->close();
    $order_detail_stmt->close();
    $payment_stmt->close();
    $update_stock_stmt->close();
    $conn->close();
} else {
    $message = "Invalid request. Payment or product details are missing.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success</title>
    <link rel="stylesheet" href="../../CSS/index.css">
    <style>
        .success-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            text-align: center;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .success-container h1 {
            color: #4CAF50;
            font-size: 2rem;
        }
        .success-container p {
            font-size: 1.2rem;
            color: #555;
        }
        .success-container a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 1rem;
            color: white;
            background-color: #4CAF50;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .success-container a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <h1>Order Confirmation</h1>
        <p><?php echo htmlspecialchars($message); ?></p>
        <a href="index.php">Return to Home</a>
    </div>
</body>
</html>
