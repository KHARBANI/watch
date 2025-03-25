<?php
require_once '../../PHP/db_connection.php';

session_start();

if (isset($_GET['order_id']) && isset($_SESSION['dealer_id'])) {
    $order_id = intval($_GET['order_id']);
    $dealer_id = intval($_SESSION['dealer_id']);

    $sql = "SELECT o.Order_ID, c.Customer_Name, p.Model_Name, od.Quantity, od.Unit_Price, o.Order_Status, o.Order_Date, o.Order_Time, s.Street_Address, s.City_ID, s.Postal_Code
            FROM order_table o
            JOIN customer_table c ON o.Customer_ID = c.Customer_ID
            JOIN order_detail_table od ON o.Order_ID = od.Order_ID
            JOIN product_table p ON od.Watch_ID = p.Watch_ID
            JOIN shipping_address_table s ON o.Shipping_Address_ID = s.Shipping_Address_ID
            WHERE o.Order_ID = ? AND p.Dealer_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $order_id, $dealer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<h2>Order Details</h2>
              <p><strong>Order ID:</strong> {$row['Order_ID']}</p>
              <p><strong>Customer Name:</strong> {$row['Customer_Name']}</p>
              <p><strong>Product:</strong> {$row['Model_Name']}</p>
              <p><strong>Quantity:</strong> {$row['Quantity']}</p>
              <p><strong>Unit Price:</strong> {$row['Unit_Price']}</p>
              <p><strong>Total Price:</strong> " . ($row['Quantity'] * $row['Unit_Price']) . "</p>
              <p><strong>Status:</strong> {$row['Order_Status']}</p>
              <p><strong>Order Date:</strong> {$row['Order_Date']}</p>
              <p><strong>Order Time:</strong> {$row['Order_Time']}</p>
              <p><strong>Shipping Address:</strong> {$row['Street_Address']}, City ID: {$row['City_ID']}, Postal Code: {$row['Postal_Code']}</p>";
    } else {
        echo "<p>Order details not found.</p>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<p>Invalid order ID or dealer not logged in.</p>";
}
?>
