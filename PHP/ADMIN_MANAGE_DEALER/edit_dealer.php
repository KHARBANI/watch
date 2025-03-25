<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dealer_id = intval($_POST['dealer_id']);
    $dealer_name = trim($_POST['dealer_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $state_id = intval($_POST['state']);
    $city_id = intval($_POST['city']);
    $brand_id = intval($_POST['brand']);
    $street = trim($_POST['street']);
    $postal_code = trim($_POST['postal_code']);
    $gst_number = trim($_POST['gst_number']);
    $pan_number = trim($_POST['pan_number']);
    $account_status = trim($_POST['account_status']);

    // Validate required fields
    if (empty($dealer_name) || empty($email) || empty($phone) || empty($state_id) || empty($city_id) || empty($brand_id) || empty($street) || empty($postal_code) || empty($gst_number) || empty($pan_number)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    // Update the address
    $query = "UPDATE address_table SET Street_Address = ?, City_ID = ?, Postal_Code = ? WHERE Address_ID = (SELECT Address_ID FROM dealer_table WHERE Dealer_ID = ?)";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        error_log('Prepare failed: ' . htmlspecialchars($conn->error));
        echo json_encode(['success' => false, 'message' => 'Database error.']);
        exit;
    }
    $stmt->bind_param('sisi', $street, $city_id, $postal_code, $dealer_id);
    if (!$stmt->execute()) {
        error_log('Execute failed: ' . htmlspecialchars($stmt->error));
        echo json_encode(['success' => false, 'message' => 'Failed to update address.']);
        exit;
    }
    $stmt->close();

    // Update the dealer
    $query = "UPDATE dealer_table SET Dealer_Name = ?, Email = ?, Phone = ?, Brand_ID = ?, GST_Number = ?, PAN_Number = ?, Account_Status = ? WHERE Dealer_ID = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        error_log('Prepare failed: ' . htmlspecialchars($conn->error));
        echo json_encode(['success' => false, 'message' => 'Database error.']);
        exit;
    }
    $stmt->bind_param('sssisssi', $dealer_name, $email, $phone, $brand_id, $gst_number, $pan_number, $account_status, $dealer_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        error_log('Execute failed: ' . htmlspecialchars($stmt->error));
        echo json_encode(['success' => false, 'message' => 'Failed to update dealer.']);
    }

    $stmt->close();
    $conn->close();
}
?>
