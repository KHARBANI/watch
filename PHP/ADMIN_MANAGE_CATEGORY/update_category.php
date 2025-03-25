<?php
require_once '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['id'])) {
    $categoryId = $conn->real_escape_string($_GET['id']);
    $categoryName = $conn->real_escape_string($_POST['editCategoryName']);
    $categoryStatus = $conn->real_escape_string($_POST['editCategoryStatus']);

    $updateCategory = "UPDATE category_table SET Category_Name = '$categoryName', Category_Status = '$categoryStatus' WHERE Category_ID = '$categoryId'";
    if ($conn->query($updateCategory) === TRUE) {
        echo 'success';
    } else {
        echo 'Error updating category: ' . $conn->error;
    }
} else {
    echo 'Invalid request';
}

$conn->close();
?>
