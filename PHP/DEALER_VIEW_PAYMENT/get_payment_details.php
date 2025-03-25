<?php
require_once('../../PHP/db_connection.php');

session_start();

if (isset($_GET['payment_id']) && isset($_SESSION['dealer_id'])) {
    $paymentId = intval($_GET['payment_id']);
    $dealer_id = intval($_SESSION['dealer_id']);

    $sql = "SELECT p.Payment_ID, c.Customer_Name, pp.Model_Name, p.Amount, p.Payment_Date, 
                   p.Order_ID, p.Payment_Method, p.Payment_Time
            FROM payment_table p
            JOIN order_detail_table od ON p.Order_ID = od.Order_ID
            JOIN product_table pp ON od.Watch_ID = pp.Watch_ID
            JOIN order_table o ON p.Order_ID = o.Order_ID
            JOIN customer_table c ON o.Customer_ID = c.Customer_ID
            WHERE p.Payment_ID = ? AND pp.Dealer_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $paymentId, $dealer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    echo json_encode($data);

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'Invalid payment ID or dealer not logged in.']);
}
?>
