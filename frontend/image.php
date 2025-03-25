<?php
// Database connection
$servername = "localhost";
$username = "root"; // replace with your database username
$password = "123456"; // replace with your database password
$dbname = "image"; // replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Path to the image file (make sure this is an actual image file)
$imagePath = 'D:\frontend\CITY1.jpg'; // Change this to the correct image file path

// Read the image file
$imageData = file_get_contents($image);

// Prepare the SQL statement
$stmt = $conn->prepare("INSERT INTO image (image_id, image) VALUES (?, ?)");
$imageId = 1; // Set your image ID (make sure it's unique)

// Bind parameters
$stmt->bind_param("ib", $imageId, $imageData); // Use "ib" for integer and blob

// Execute the statement
if ($stmt->execute()) {
    echo "Image inserted successfully.";
} else {
    echo "Error inserting image: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>