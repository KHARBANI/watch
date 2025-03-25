<?php
require_once '../db_connection.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    $categoryId = $conn->real_escape_string($_GET['id']);
    $newStatus = $conn->real_escape_string($_GET['status']);
    $toggleCategoryStatus = "UPDATE category_table SET Category_Status = '$newStatus' WHERE Category_ID = '$categoryId'";
    if ($conn->query($toggleCategoryStatus) === TRUE) {
        echo 'success';
    } else {
        echo 'Error updating category status: ' . $conn->error;
    }
} else {
    echo 'Invalid request';
}

$conn->close();
?>
