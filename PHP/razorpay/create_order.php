<?php
// Include the Razorpay PHP library (ensure you have installed it via Composer)
require('vendor/autoload.php');

use Razorpay\Api\Api;

// Replace these with your actual Razorpay Test API credentials
$keyId = "rzp_test_QoxUlzfLT9H8al";
$keySecret = "your_test_key_secret"; // Replace with your actual key secret

// Initialize the Razorpay API
$api = new Api($keyId, $keySecret);

// Set order details
$orderData = [
    'receipt'         => 'order_rcptid_11',  // Unique identifier for your order
    'amount'          => 50000,              // Amount in paise (₹500.00)
    'currency'        => 'INR',
    'payment_capture' => 1                   // Auto capture payments
];

// Create order on Razorpay
try {
    $order = $api->order->create($orderData);
    // You can send this order data (especially $order['id']) back to your client-side
    header('Content-Type: application/json');
    echo json_encode($order);
} catch (Exception $e) {
    // Handle exception if order creation fails
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => $e->getMessage()]);
}
?>
