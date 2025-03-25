<?php
require_once '../../PHP/db_connection.php';

$query = "SELECT Strap_ID, Strap_Name FROM strap_material_table";
$result = mysqli_query($conn, $query);

$strapMaterials = [];
while ($row = mysqli_fetch_assoc($result)) {
    $strapMaterials[] = $row;
}

echo json_encode($strapMaterials);
?>
