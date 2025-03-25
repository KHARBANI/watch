<?php
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $city_name = trim($_POST['city_name']);
    $state_id = intval($_POST['state_id']);

    if (empty($city_name)) {
        echo json_encode(['success' => false, 'message' => 'City name cannot be empty.']);
        exit;
    }

    if (empty($state_id)) {
        echo json_encode(['success' => false, 'message' => 'State ID cannot be empty.']);
        exit;
    }

    // Check if the city already exists in the selected state
    $query = "SELECT City_ID FROM city_table WHERE City_Name = ? AND State_ID = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        error_log('Prepare failed: ' . htmlspecialchars($conn->error));
        echo json_encode(['success' => false, 'message' => 'Database error.']);
        exit;
    }
    $stmt->bind_param('si', $city_name, $state_id);
    if (!$stmt->execute()) {
        error_log('Execute failed: ' . htmlspecialchars($stmt->error));
        echo json_encode(['success' => false, 'message' => 'Database error.']);
        exit;
    }
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'City already exists in the selected state.']);
    } else {
        // Insert the new city
        $query = "INSERT INTO city_table (City_Name, State_ID) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            error_log('Prepare failed: ' . htmlspecialchars($conn->error));
            echo json_encode(['success' => false, 'message' => 'Database error.']);
            exit;
        }
        $stmt->bind_param('si', $city_name, $state_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'city_id' => $stmt->insert_id]);
        } else {
            error_log('Execute failed: ' . htmlspecialchars($stmt->error));
            echo json_encode(['success' => false, 'message' => 'Failed to add city.']);
        }
    }

    $stmt->close();
    $conn->close();
}
?>
