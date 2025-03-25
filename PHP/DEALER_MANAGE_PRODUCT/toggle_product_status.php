<?php
require_once '../../PHP/db_connection.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    $productId = intval($_GET['id']);
    $newStatus = ucfirst($_GET['status']); // Capitalize status

    $query = "UPDATE product_table SET Product_Status = ? WHERE Watch_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $newStatus, $productId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update product status']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
}

$conn->close();
?>
