<?php
require_once '../../PHP/db_connection.php';

$query = "SELECT Case_ID, Case_Name FROM case_material_table";
$result = mysqli_query($conn, $query);

$caseMaterials = [];
while ($row = mysqli_fetch_assoc($result)) {
    $caseMaterials[] = $row;
}

echo json_encode($caseMaterials);
?>
