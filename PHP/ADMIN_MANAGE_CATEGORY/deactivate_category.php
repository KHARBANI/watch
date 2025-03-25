<?php
require_once '../db_connection.php';

if (isset($_GET['id'])) {
    $categoryId = $conn->real_escape_string($_GET['id']);
    $deactivateCategory = "UPDATE category_table SET Category_Status = 'Inactive' WHERE Category_ID = '$categoryId'";
    if ($conn->query($deactivateCategory) === TRUE) {
        echo 'success';
    } else {
        echo 'Error deactivating category: ' . $conn->error;
    }
} else {
    echo 'Invalid request';
}

$conn->close();
?>
