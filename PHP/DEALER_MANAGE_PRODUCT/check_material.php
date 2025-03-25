<?php
require_once '../../PHP/db_connection.php';

$type = $_GET['type'];
$name = $_GET['name'];

if ($type && $name) {
    $table = '';
    $column = '';

    switch ($type) {
        case 'case':
            $table = 'case_material_table';
            $column = 'Case_Name';
            break;
        case 'strap':
            $table = 'strap_material_table';
            $column = 'Strap_Name';
            break;
        case 'movement':
            $table = 'movement_table';
            $column = 'Movement_Name';
            break;
        default:
            echo json_encode(['exists' => false]);
            exit;
    }

    $query = "SELECT COUNT(*) AS count FROM $table WHERE $column = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    echo json_encode(['exists' => $row['count'] > 0]);
} else {
    echo json_encode(['exists' => false]);
}
?>
