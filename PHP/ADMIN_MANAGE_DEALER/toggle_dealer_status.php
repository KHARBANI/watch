<?php
require_once 'db_connection.php';

if (isset($_POST['dealer_id'])) {
    $dealer_id = intval($_POST['dealer_id']);
    
    // Fetch the current account status
    $query = "SELECT Account_Status FROM dealer_table WHERE Dealer_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $dealer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $dealer = $result->fetch_assoc();
    
    // Toggle the account status
    $new_status = $dealer['Account_Status'] === 'Active' ? 'Inactive' : 'Active';
    $update_query = "UPDATE dealer_table SET Account_Status = ? WHERE Dealer_ID = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param('si', $new_status, $dealer_id);
    $update_stmt->execute();
    
    $update_stmt->close();
    $stmt->close();
}

$conn->close();
header('Location: admin_manage_dealer.php');
exit();
?>
