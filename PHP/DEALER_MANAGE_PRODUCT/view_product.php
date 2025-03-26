<?php
require_once '../../PHP/db_connection.php';

if (isset($_GET['id'])) {
    $productId = intval($_GET['id']);
    
    $query = "
        SELECT 
            p.Watch_ID AS id, 
            p.Model_Name AS name, 
            c.Category_Name AS category, 
            p.Price AS price, 
            p.Stock_Quantity AS stock,
            i.Image_URL AS image,
            p.Product_Status AS status,
            p.Product_Description AS description,
            cm.Case_Name AS case_material,
            sm.Strap_Name AS strap_material,
            m.Movement_Name AS movement_type
        FROM 
            product_table p
        JOIN 
            category_table c ON p.Category_ID = c.Category_ID
        JOIN 
            image_table i ON p.Image_ID = i.Image_ID
        JOIN 
            case_material_table cm ON p.Case_ID = cm.Case_ID
        JOIN 
            strap_material_table sm ON p.Strap_ID = sm.Strap_ID
        JOIN 
            movement_table m ON p.Movement_ID = m.Movement_ID
        WHERE 
            p.Watch_ID = $productId
    ";
    
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
        // Update image path
        $product['image'] = basename($product['image']);
        $product['status'] = ucfirst($product['status']); // Capitalize status
        echo json_encode($product);
    } else {
        echo json_encode(['error' => 'Product not found']);
    }
} else {
    echo json_encode(['error' => 'Invalid product ID']);
}
?>
