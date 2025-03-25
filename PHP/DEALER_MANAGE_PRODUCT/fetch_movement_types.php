<?php
require_once '../../PHP/db_connection.php';

$query = "SELECT Movement_ID, Movement_Name FROM movement_table";
$result = mysqli_query($conn, $query);

$movementTypes = [];
while ($row = mysqli_fetch_assoc($result)) {
    $movementTypes[] = $row;
}

echo json_encode($movementTypes);
?>
