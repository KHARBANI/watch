<?php
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $state_name = trim($_POST['state_name']);

    // Check if the state already exists
    $query = "SELECT State_ID FROM state_table WHERE State_Name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $state_name);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'State already exists.']);
    } else {
        // Insert the new state
        $query = "INSERT INTO state_table (State_Name) VALUES (?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $state_name);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'state_id' => $stmt->insert_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add state.']);
        }
    }

    $stmt->close();
    $conn->close();
}
?>
