<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "watch_store");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    // Validate user in customer_table or dealer_table
    $sql = "SELECT 'customer' AS user_type FROM customer_table WHERE Email = ? AND Customer_Name = ? AND Account_Status = 'Active'
            UNION
            SELECT 'dealer' AS user_type FROM dealer_table WHERE Email = ? AND Dealer_Name = ? AND Account_Status = 'Active'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $email, $name, $email, $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User is valid, insert the message into customer_support_table
        $insertSql = "INSERT INTO customer_support_table (Email, Name, Message) VALUES (?, ?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("sss", $email, $name, $message);

        if ($insertStmt->execute()) {
            echo "Message sent successfully!";
        } else {
            echo "Failed to send the message. Please try again.";
        }

        $insertStmt->close();
    } else {
        // User is not valid
        echo "You are not authorized to send a message. Please ensure your account is active.";
    }

    $stmt->close();
}

$conn->close();
?>
