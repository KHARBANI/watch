<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "watch_store"; 
$conn = new mysqli($servername, $username, $password, $dbname); 

if ($conn->connect_error) 
{ 
    die("Connection Failed: " . $conn->connect_error);
} 
else 
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") 
    { 
        $input_email = $_POST["email"]; 
        $input_password = $_POST["password"];

        if($input_email==="watchstore@gmail.com" && $input_password==="admin123")
        {
            echo "<script>alert('Admin login successful! Redirecting to admin dashboard...');</script>";
            header("Refresh:1; url=../PHP/admin_dashboard.php");
        }
        else
        {
            // Fetch the hashed password from the database
            $sql = "SELECT Password FROM customer_table WHERE Email = '$input_email'"; 
            $result = $conn->query($sql); 

            if ($result->num_rows === 1) 
            { 
                $row = $result->fetch_assoc();
                $hashed_password = $row["Password"];

                // Verify the password
                if (password_verify($input_password, $hashed_password)) 
                {
                    echo "<script>alert('Login successful! Welcome, $input_email.');</script>";
                } 
                else 
                {
                    echo "<script>alert('Error: Invalid password.');</script>";
                }
            } 
            else 
            { 
                echo "<script>alert('Error: Email not found.');</script>";
            } 
        }
    } 
    $conn->close();
} 
?>