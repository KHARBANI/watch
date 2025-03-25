<?php
require_once '../PHP/db_connection.php';

if (isset($_POST['state_id'])) {
    $stateId = $_POST['state_id'];

    $sql = "SELECT City_ID, City_Name FROM city_table WHERE State_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $stateId);
    $stmt->execute();
    $result = $stmt->get_result();

    $cities = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $cities[] = $row;
        }
    }

    echo json_encode($cities);
}

$conn->close();
?>
