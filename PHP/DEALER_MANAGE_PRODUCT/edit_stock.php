<?php
require_once '../../PHP/db_connection.php';

$stockId = isset($_GET['stock_id']) ? intval($_GET['stock_id']) : 0;
$stockQuantity = isset($_POST['editStockQuantity']) ? intval($_POST['editStockQuantity']) : 0;
$minStockLevel = isset($_POST['editMinStockLevel']) ? intval($_POST['editMinStockLevel']) : 0;

$query = "
    UPDATE stock_table
    SET 
        Quantity_Available = $stockQuantity,
        Minimum_Stock_Level = $minStockLevel,
        Last_Updated = NOW()
    WHERE 
        Stock_ID = $stockId
";

if (mysqli_query($conn, $query)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update stock.']);
}
?>
