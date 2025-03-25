<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

try {
    if (isset($_GET['state_id'])) {
        $state_id = intval($_GET['state_id']);
        $query = "SELECT City_ID, City_Name FROM city_table WHERE State_ID = ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param("i", $state_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $cities = [];
        while ($row = $result->fetch_assoc()) {
            $cities[] = $row;
        }
        $stmt->close();
        $conn->close();
        echo json_encode($cities);
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Invalid state ID"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
