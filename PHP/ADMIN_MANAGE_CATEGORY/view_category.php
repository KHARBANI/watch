<?php
require_once '../db_connection.php';

if (isset($_GET['id'])) {
    $categoryId = $conn->real_escape_string($_GET['id']);
    $sql = "SELECT * FROM category_table WHERE Category_ID = '$categoryId'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $category = $result->fetch_assoc();
        echo "<h2>Category Details</h2>";
        echo "<p><strong>Category_ID:</strong> " . $category['Category_ID'] . "</p>";
        echo "<p><strong>Category_Name:</strong> " . $category['Category_Name'] . "</p>";
        echo "<p><strong>Category_Status:</strong> " . $category['Category_Status'] . "</p>";
    } else {
        echo "<p>Category not found.</p>";
    }
} else {
    echo "<p>Invalid request.</p>";
}

$conn->close();
?>
