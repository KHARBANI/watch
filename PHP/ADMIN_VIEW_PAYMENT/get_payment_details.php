<?php
require_once '../../PHP/db_connection.php';

if (isset($_GET['payment_id'])) {
    $payment_id = $_GET['payment_id'];

    $query = "
        SELECT 
            p.Payment_ID, 
            o.Order_ID, 
            p.Payment_Method, 
            p.Amount, 
            p.Payment_Date, 
            p.Payment_Time
        FROM 
            payment_table p
        JOIN 
            order_table o ON p.Order_ID = o.Order_ID
        WHERE 
            p.Payment_ID = $payment_id
    ";
    $result = mysqli_query($conn, $query);
    $payment = mysqli_fetch_assoc($result);

    if ($payment) {
        echo "<h2>Payment Details</h2>";
        echo "<p><strong>Payment ID:</strong> " . $payment['Payment_ID'] . "</p>";
        echo "<p><strong>Order ID:</strong> " . $payment['Order_ID'] . "</p>";
        echo "<p><strong>Payment Method:</strong> " . $payment['Payment_Method'] . "</p>";
        echo "<p><strong>Amount:</strong> " . $payment['Amount'] . "</p>";
        echo "<p><strong>Payment Date:</strong> " . $payment['Payment_Date'] . "</p>";
        echo "<p><strong>Payment Time:</strong> " . $payment['Payment_Time'] . "</p>";
    } else {
        echo "<p>Payment details not found.</p>";
    }
} else {
    echo "<p>Invalid request.</p>";
}
?>
