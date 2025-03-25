<?php
require_once '../../PHP/db_connection.php'; // Ensure this file contains the database connection logic

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $watchId = mysqli_real_escape_string($conn, $_POST['watchId']);
    $stockQuantity = mysqli_real_escape_string($conn, $_POST['stockQuantity']);

    $query = "
        UPDATE product_table
        SET Stock_Quantity = '$stockQuantity', Created_At = NOW()
        WHERE Watch_ID = '$watchId'
    ";

    if (mysqli_query($conn, $query)) {
        header('Location: dealer_manage_products.php');
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }
}
?>
