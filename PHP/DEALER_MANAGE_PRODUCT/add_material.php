<?php
require_once '../../PHP/db_connection.php'; // Correct the path to the database connection file

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json'); // Ensure the response is JSON

$type = $_POST['type'];
$name = $_POST['name'];

$response = ['success' => false, 'message' => '', 'id' => null];

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
            $response['message'] = 'Invalid type';
            echo json_encode($response);
            exit;
    }

    $stmt = $conn->prepare("INSERT INTO $table ($column) VALUES (?)");
    $stmt->bind_param('s', $name);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = ucfirst($type) . ' material added successfully';
        $response['id'] = $stmt->insert_id;
    } else {
        $response['message'] = 'Failed to add ' . $type . ' material';
    }

    $stmt->close();
} else {
    $response['message'] = 'Type and name are required';
}

$conn->close();
echo json_encode($response);
?>
