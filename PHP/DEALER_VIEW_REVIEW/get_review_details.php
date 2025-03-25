<?php
require_once '../../PHP/db_connection.php';

session_start();

if (isset($_GET['review_id']) && isset($_SESSION['dealer_id'])) {
    $review_id = intval($_GET['review_id']);
    $dealer_id = intval($_SESSION['dealer_id']);

    // Fetch review details with joined tables
    $query = "
        SELECT 
            r.Review_ID, c.Customer_Name, r.Rating, r.Review_Details, r.Review_Date, r.Order_ID, od.Watch_ID
        FROM 
            review_table r
        JOIN 
            customer_table c ON r.Customer_ID = c.Customer_ID
        JOIN 
            order_detail_table od ON r.Order_ID = od.Order_ID
        JOIN 
            product_table p ON od.Watch_ID = p.Watch_ID
        WHERE 
            r.Review_ID = ? AND p.Dealer_ID = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $review_id, $dealer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $review_details = $result->fetch_assoc();

    header('Content-Type: application/json');
    echo json_encode($review_details);

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'Invalid review ID or dealer not logged in.']);
}
?>
