<?php
require_once '../../PHP/db_connection.php';
session_start();

// Ensure dealer is logged in
if (!isset($_SESSION['dealer_id'])) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

$dealerId = $_SESSION['dealer_id'];

$query = "
    SELECT 
        s.Stock_ID AS id,
        s.Watch_ID AS watch_id,
        s.Quantity_Available AS stock, 
        s.Minimum_Stock_Level AS min_stock, 
        s.Last_Updated AS last_updated
    FROM 
        stock_table s
    JOIN 
        product_table p ON s.Watch_ID = p.Watch_ID
    WHERE 
        p.Dealer_ID = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $dealerId);
$stmt->execute();
$result = $stmt->get_result();

$stocks = [];
while ($row = mysqli_fetch_assoc($result)) {
    $stocks[] = $row;
}

header('Content-Type: application/json');
echo json_encode($stocks);
?>
