<?php
require_once '../../PHP/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $authSql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($authSql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $authResult = $stmt->get_result();

    if ($authResult->num_rows > 0) {
        $user = $authResult->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // User is authenticated
            echo "<script>alert('Login successful!');</script>";
        } else {
            // Authentication failed
            echo "<script>alert('Invalid email or password.');</script>";
        }
    } else {
        // Authentication failed
        echo "<script>alert('Invalid email or password.');</script>";
    }
    $stmt->close();
}
?>
