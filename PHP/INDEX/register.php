<?php
require_once '../../PHP/db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Validate input
    if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($confirmPassword)) {
        echo "<script>showPopupMessage('All fields are required.', 'error');</script>";
        exit();
    }

    if ($password !== $confirmPassword) {
        echo "<script>showPopupMessage('Passwords do not match.', 'error');</script>";
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert into the customer table with Account_Status set to 'active'
    $insertSql = "INSERT INTO customer_table (Customer_Name, Email, Phone, Password, Account_Status) VALUES (?, ?, ?, ?, 'active')";
    $stmt = $conn->prepare($insertSql);
    $stmt->bind_param("ssss", $name, $email, $phone, $hashedPassword);

    if ($stmt->execute()) {
        echo "<script>showPopupMessage('Registration successful! Please log in.', 'success');</script>";
        echo "<script>window.location.href = '../../PHP/INDEX/index.php';</script>";
    } else {
        echo "<script>showPopupMessage('Registration failed. Please try again.', 'error');</script>";
    }

    $stmt->close();
}
?>