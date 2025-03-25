<?php
require_once '../../PHP/db_connection.php';

$review_id = $_GET['review_id'];

// Fetch review details with joined tables
$query = "
    SELECT 
        r.Review_ID, r.Customer_ID, r.Rating, r.Review_Details, r.Review_Date, r.Order_ID, od.Watch_ID
    FROM 
        review_table r
    JOIN 
        order_detail_table od ON r.Order_ID = od.Order_ID
    WHERE 
        r.Review_ID = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $review_id);
$stmt->execute();
$result = $stmt->get_result();
$review_details = $result->fetch_assoc();

header('Content-Type: application/json');
echo json_encode($review_details);
?>
