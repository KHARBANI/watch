<?php
require_once '../../PHP/db_connection.php'; // Correct the path to the database connection file

header('Content-Type: application/json'); // Ensure the response is JSON

$response = ['success' => false, 'message' => ''];

$watchId = intval($_POST['watch_id']);
$stockQuantity = intval($_POST['stockQuantity']);
$minStockLevel = intval($_POST['minStockLevel']);

if ($watchId && $stockQuantity && $minStockLevel) {
    // Check if the product exists
    $productCheckQuery = "SELECT Watch_ID FROM product_table WHERE Watch_ID = ?";
    $stmt = $conn->prepare($productCheckQuery);
    $stmt->bind_param('i', $watchId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();

        // Check if the Watch_ID already exists in the stock table
        $query = "SELECT Quantity_Available, Minimum_Stock_Level FROM stock_table WHERE Watch_ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $watchId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Watch_ID exists, increment the quantity and minimum stock level
            $row = $result->fetch_assoc();
            $newQuantity = $row['Quantity_Available'] + $stockQuantity;
            $newMinStockLevel = $row['Minimum_Stock_Level'] + $minStockLevel;
            $updateQuery = "UPDATE stock_table SET Quantity_Available = ?, Minimum_Stock_Level = ?, Last_Updated = NOW() WHERE Watch_ID = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("iii", $newQuantity, $newMinStockLevel, $watchId);
            if ($updateStmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Stock updated successfully';
            } else {
                $response['message'] = 'Failed to update stock';
            }
        } else {
            // Watch_ID does not exist, insert a new record
            $insertQuery = "INSERT INTO stock_table (Watch_ID, Quantity_Available, Minimum_Stock_Level, Last_Updated) VALUES (?, ?, ?, NOW())";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("iii", $watchId, $stockQuantity, $minStockLevel);
            if ($insertStmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Stock added successfully';
            } else {
                $response['message'] = 'Failed to add stock';
            }
        }

        $stmt->close();
    } else {
        $response['message'] = 'Product does not exist';
        $stmt->close();
    }
} else {
    $response['message'] = 'All fields are required';
}

$conn->close();
echo json_encode($response);
?>
