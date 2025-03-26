<?php
require_once '../../PHP/db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $usertype = $_POST['usertype'];

    if ($usertype == 'dealer') {
        $authSql = "SELECT * FROM dealer_table WHERE Email = ?";
    } elseif ($usertype == 'customer') {
        $authSql = "SELECT * FROM customer_table WHERE Email = ?";
    } else {
        echo "<script>showPopupMessage('Invalid user type.', 'error');</script>";
        exit();
    }

    $stmt = $conn->prepare($authSql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $authResult = $stmt->get_result();

    if ($authResult->num_rows > 0) {
        $user = $authResult->fetch_assoc();
        if (password_verify($password, $user['Password'])) {
            // User is authenticated
            if ($usertype == 'dealer') {
                $_SESSION['dealer_id'] = $user['Dealer_ID'];
                setcookie('dealer_id', $user['Dealer_ID'], time() + (86400 * 30), "/"); // 86400 = 1 day
                echo "<script>showPopupMessage('Login successful! Redirecting to dealer dashboard.', 'success');</script>";
                // Redirect to dealer dashboard
                echo "<script>window.location.href = '../../PHP/DEALER_DASHBOARD/dealer_dashboard.php';</script>";
            } else {
                $_SESSION['user_id'] = $user['Customer_ID']; // Store customer ID in session
                $_SESSION['user_name'] = $user['Name']; // Store customer name in session
                echo "<script>showPopupMessage('Login successful!', 'success');</script>";
                // Redirect to customer homepage (index.php)
                echo "<script>window.location.href = 'index.php';</script>";
            }
        } else {
            // Authentication failed
            echo "<script>showPopupMessage('Invalid email or password.', 'error');</script>";
        }
    } else {
        // Authentication failed
        echo "<script>showPopupMessage('Invalid email or password.', 'error');</script>";
    }
    $stmt->close();
}
?>
