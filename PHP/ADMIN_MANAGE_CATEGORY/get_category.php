<?php
require_once '../db_connection.php';

if (isset($_GET['id'])) {
    $categoryId = $conn->real_escape_string($_GET['id']);
    $sql = "SELECT * FROM category_table WHERE Category_ID = '$categoryId'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $category = $result->fetch_assoc();
        echo json_encode($category);
    } else {
        echo json_encode(['error' => 'Category not found.']);
    }
} else {
    echo json_encode(['error' => 'Invalid request.']);
}

$conn->close();
?>
