<?php
require_once '../../PHP/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = intval($_POST['productId']);
    $productName = mysqli_real_escape_string($conn, $_POST['productName']);
    $productPrice = floatval($_POST['productPrice']);
    $productDescription = mysqli_real_escape_string($conn, $_POST['productDescription']);
    $caseMaterial = intval($_POST['caseMaterial']);
    $strapMaterial = intval($_POST['strapMaterial']);
    $movementType = intval($_POST['movementType']);
    $stockQuantity = intval($_POST['stockQuantity']); // Fetch stock quantity from POST data

    // Handle image upload if a new image is provided
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $targetDir = "../../uploads/";
        $targetFile = $targetDir . basename($image);

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $query = "
                UPDATE product_table
                SET Model_Name = '$productName', Price = $productPrice, Product_Description = '$productDescription', 
                    Case_ID = $caseMaterial, Strap_ID = $strapMaterial, Movement_ID = $movementType, 
                    Stock_Quantity = $stockQuantity, 
                    Image_ID = (SELECT Image_ID FROM image_table WHERE Image_URL = '$image')
                WHERE Watch_ID = $productId
            ";
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
            exit();
        }
    } else {
        $query = "
            UPDATE product_table
            SET Model_Name = '$productName', Price = $productPrice, Product_Description = '$productDescription', 
                Case_ID = $caseMaterial, Strap_ID = $strapMaterial, Movement_ID = $movementType, 
                Stock_Quantity = $stockQuantity
            WHERE Watch_ID = $productId
        ";
    }

    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating record: ' . mysqli_error($conn)]);
    }
}
?>
