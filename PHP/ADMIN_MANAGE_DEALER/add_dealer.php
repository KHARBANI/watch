<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dealer_name = trim($_POST['dealer_name']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $phone = trim($_POST['phone']);
    $state_id = intval($_POST['state']);
    $city_id = intval($_POST['city']);
    $brand_id = intval($_POST['brand']);
    $street = trim($_POST['street']);
    $postal_code = trim($_POST['postal_code']);
    $gst_number = trim($_POST['gst_number']);
    $pan_number = trim($_POST['pan_number']);
    $account_status = 'Active';

    // Validate required fields
    if (empty($dealer_name) || empty($email) || empty($password) || empty($phone) || empty($state_id) || empty($city_id) || empty($brand_id) || empty($street) || empty($postal_code) || empty($gst_number) || empty($pan_number)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    // Insert the address
    $query = "INSERT INTO address_table (Street_Address, City_ID, Postal_Code) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        error_log('Prepare failed: ' . htmlspecialchars($conn->error));
        echo json_encode(['success' => false, 'message' => 'Database error.']);
        exit;
    }
    $stmt->bind_param('sis', $street, $city_id, $postal_code);
    if ($stmt->execute()) {
        $address_id = $stmt->insert_id;
    } else {
        error_log('Execute failed: ' . htmlspecialchars($stmt->error));
        echo json_encode(['success' => false, 'message' => 'Failed to add address.']);
        exit;
    }
    $stmt->close();

    // Insert the new dealer
    $query = "INSERT INTO dealer_table (Dealer_Name, Email, Password, Phone, Address_ID, Brand_ID, GST_Number, PAN_Number, Account_Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        error_log('Prepare failed: ' . htmlspecialchars($conn->error));
        echo json_encode(['success' => false, 'message' => 'Database error.']);
        exit;
    }
    $stmt->bind_param('ssssiiiss', $dealer_name, $email, $password, $phone, $address_id, $brand_id, $gst_number, $pan_number, $account_status);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        error_log('Execute failed: ' . htmlspecialchars($stmt->error));
        echo json_encode(['success' => false, 'message' => 'Failed to add dealer.']);
    }

    $stmt->close();
    $conn->close();
}
?>
