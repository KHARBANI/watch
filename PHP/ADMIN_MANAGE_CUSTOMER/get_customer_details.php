<?php
require_once '../db_connection.php';

if (isset($_GET['id'])) {
    $customerId = $_GET['id'];
    $sql = "SELECT * FROM customer_table WHERE Customer_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $customer = $result->fetch_assoc();
        echo "<h2>Customer Details</h2>";
        echo "<p><strong>ID:</strong> " . htmlspecialchars($customer['Customer_ID']) . "</p>";
        echo "<p><strong>Name:</strong> " . htmlspecialchars($customer['Customer_Name']) . "</p>";
        echo "<p><strong>Email:</strong> " . htmlspecialchars($customer['Email']) . "</p>";
        echo "<p><strong>Phone:</strong> " . htmlspecialchars($customer['Phone']) . "</p>";
        echo "<p><strong>Account Status:</strong> " . htmlspecialchars($customer['Account_Status']) . "</p>";
    } else {
        echo "<p>No customer found with the given ID.</p>";
    }

    $stmt->close();
} else {
    echo "<p>Invalid request.</p>";
}

$conn->close();
?>