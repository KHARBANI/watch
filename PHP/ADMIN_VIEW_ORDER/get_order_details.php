<?php
require_once '../db_connection.php';

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    $query = "SELECT o.Order_ID, c.Customer_Name, p.Model_Name, od.Quantity, od.Unit_Price, od.Subtotal, o.Total_Price, o.Order_Status, o.Order_Date, o.Order_Time, s.Street_Address, ci.City_Name, st.State_Name, s.Postal_Code
              FROM order_table o
              JOIN Customer_table c ON o.Customer_ID = c.Customer_ID
              JOIN Order_Detail_table od ON o.Order_ID = od.Order_ID
              JOIN Product_table p ON od.Watch_ID = p.Watch_ID
              JOIN Shipping_Address_table s ON o.Shipping_Address_ID = s.Shipping_Address_ID
              JOIN City_table ci ON s.City_ID = ci.City_ID
              JOIN State_table st ON ci.State_ID = st.State_ID
              WHERE o.Order_ID = ?";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $order_details = $result->fetch_assoc();
        $stmt->close();
        
        echo json_encode($order_details);
    } else {
        echo json_encode(['error' => 'Failed to prepare statement']);
    }
} else {
    echo json_encode(['error' => 'Order ID not provided']);
}

$conn->close();
?>
