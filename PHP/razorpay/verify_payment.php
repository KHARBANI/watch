<?php
// Include the Razorpay PHP library
require('vendor/autoload.php');

use Razorpay\Api\Api;

// Replace these with your actual Razorpay Test API credentials
$keyId = "rzp_test_QoxUlzfLT9H8al";
$keySecret = "your_test_key_secret"; // Replace with your actual key secret

// Retrieve POST data from Razorpay checkout response
$razorpay_order_id   = $_POST['razorpay_order_id'] ?? '';
$razorpay_payment_id = $_POST['razorpay_payment_id'] ?? '';
$razorpay_signature  = $_POST['razorpay_signature'] ?? '';

// Verify that all required parameters are received
if (empty($razorpay_order_id) || empty($razorpay_payment_id) || empty($razorpay_signature)) {
    header('HTTP/1.1 400 Bad Request');
    echo "Missing parameters for verification.";
    exit;
}

// Generate the signature using the order ID and payment ID
$generatedSignature = hash_hmac('sha256', $razorpay_order_id . '|' . $razorpay_payment_id, $keySecret);

// Compare the generated signature with the signature from Razorpay
if (hash_equals($generatedSignature, $razorpay_signature)) {
    // Payment verification successful
    // Proceed with updating your order status, etc.
    echo "Payment verified successfully.";
} else {
    // Payment verification failed
    echo "Payment verification failed.";
}
?>
