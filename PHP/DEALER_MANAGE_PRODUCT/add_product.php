<?php
// Start session to manage dealer login (if not already started)
session_start();

require_once '../../PHP/db_connection.php'; // Correct the path to the database connection file

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json'); // Ensure the response is JSON

$response = ['success' => false, 'message' => '', 'watch_id' => null];

// Ensure dealer is logged in
if (!isset($_SESSION['dealer_id'])) {
    $response['message'] = 'Please log in as a dealer to add products.';
    echo json_encode($response);
    exit();
}

$dealer_id = $_SESSION['dealer_id'];

// Fetch dealer brand
$brandQuery = "
    SELECT b.Brand_Name 
    FROM dealer_table d
    JOIN brand_table b ON d.Brand_ID = b.Brand_ID
    WHERE d.Dealer_ID = ?
";
$stmt = $conn->prepare($brandQuery);
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$brandResult = $stmt->get_result();
$brandRow = $brandResult->fetch_assoc();
$brandName = $brandRow['Brand_Name'];
$stmt->close();

$productName = $_POST['productName'];
$image = $_FILES['image'];
$productPrice = $_POST['productPrice'];
$caseMaterial = $_POST['caseMaterial'];
$strapMaterial = $_POST['strapMaterial'];
$movementType = $_POST['movementType'];
$productDescription = $_POST['productDescription'];
$productStatus = 'Active'; // Set product status to Active by default

// Determine category ID based on brand name
$categoryQuery = "SELECT Category_ID FROM category_table WHERE Category_Name = ?";
$stmt = $conn->prepare($categoryQuery);
$stmt->bind_param("s", $brandName);
$stmt->execute();
$categoryResult = $stmt->get_result();
$categoryRow = $categoryResult->fetch_assoc();
$categoryId = $categoryRow['Category_ID'];
$stmt->close();

if ($productName && $image && $productPrice && $caseMaterial && $strapMaterial && $movementType && $productDescription) {
    // Handle image upload
    $targetDir = "C:/xampp/htdocs/uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $targetFile = $targetDir . basename($image["name"]);
    if (move_uploaded_file($image["tmp_name"], $targetFile)) {
        // Insert image into the image_table
        $stmt = $conn->prepare("INSERT INTO image_table (Image_URL) VALUES (?)");
        $stmt->bind_param('s', $targetFile);

        if ($stmt->execute()) {
            $imageId = $stmt->insert_id;
            $stmt->close();

            // Insert product into the product_table
            $stmt = $conn->prepare("INSERT INTO product_table (Model_Name, Image_ID, Price, Category_ID, Case_ID, Strap_ID, Movement_ID, Product_Description, Product_Status, Dealer_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('sdiississi', $productName, $imageId, $productPrice, $categoryId, $caseMaterial, $strapMaterial, $movementType, $productDescription, $productStatus, $dealer_id);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Product added successfully';
                $response['watch_id'] = $stmt->insert_id;
                $response['popup_message'] = "Product added successfully. Product ID: " . $stmt->insert_id;
            } else {
                $response['message'] = 'Failed to add product';
            }

            $stmt->close();
        } else {
            $response['message'] = 'Failed to add image';
        }
    } else {
        $response['message'] = 'Failed to upload image';
    }
} else {
    $response['message'] = 'All fields are required';
}

$conn->close();
echo json_encode($response);
?>
