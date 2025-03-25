<?php
require_once 'db_connection.php';

// Disable error reporting or redirect errors to a log file
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/php-error.log');

if (isset($_GET['dealer_id'])) {
    $dealer_id = intval($_GET['dealer_id']);
    
    // Correct the SQL query to join the address_table and fetch the required address details
    $query = "
        SELECT d.*, a.Street_Address, a.Postal_Code, c.City_ID, c.City_Name, s.State_ID, s.State_Name
        FROM dealer_table d
        JOIN address_table a ON d.Address_ID = a.Address_ID
        JOIN city_table c ON a.City_ID = c.City_ID
        JOIN state_table s ON c.State_ID = s.State_ID
        WHERE d.Dealer_ID = ?
    ";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $stmt->bind_param('i', $dealer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $dealer = $result->fetch_assoc();

    header('Content-Type: application/json');
    echo json_encode($dealer);
    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid dealer ID']);
}

$conn->close();
?>
