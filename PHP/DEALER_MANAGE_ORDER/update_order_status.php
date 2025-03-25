<?php
require_once '../../PHP/db_connection.php';

if (isset($_POST['order_id']) && isset($_POST['order_status'])) {
    $order_id = intval($_POST['order_id']);
    $order_status = $_POST['order_status'];

    $sql = "UPDATE order_table SET Order_Status = ? WHERE Order_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $order_status, $order_id);

    if ($stmt->execute()) {
        echo "Order status updated successfully!";
    } else {
        echo "Error updating order status: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid input.";
}
