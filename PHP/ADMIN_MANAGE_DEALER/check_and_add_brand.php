<?php
require_once 'db_connection.php';

if (isset($_POST['brand_name'])) {
    $brand_name = trim($_POST['brand_name']);
    
    // Check if the brand already exists
    $stmt = $conn->prepare("SELECT Brand_ID FROM brand_table WHERE Brand_Name = ?");
    $stmt->bind_param("s", $brand_name);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Brand already exists']);
    } else {
        // Insert the new brand
        $stmt = $conn->prepare("INSERT INTO brand_table (Brand_Name) VALUES (?)");
        $stmt->bind_param("s", $brand_name);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'brand_id' => $stmt->insert_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add brand']);
        }
    }
    $stmt->close();
}
$conn->close();
?>
