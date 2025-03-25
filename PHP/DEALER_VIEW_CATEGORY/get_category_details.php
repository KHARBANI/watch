<?php
if (isset($_GET['id'])) {
    $categoryId = $_GET['id'];

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'watch_store');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch category details
    $sql = "SELECT Category_ID, Category_Name, Category_Status FROM category_table WHERE Category_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<h2>Category Details</h2>";
        echo "<p><strong>ID:</strong> " . $row["Category_ID"] . "</p>";
        echo "<p><strong>Name:</strong> " . $row["Category_Name"] . "</p>";
        echo "<p><strong>Status:</strong> " . $row["Category_Status"] . "</p>";
    } else {
        echo "<p>Category not found.</p>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<p>Invalid request.</p>";
}
?>
