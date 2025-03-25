<?php
$hostname = "localhost";
$username = "root";
$password = "";  
$dbname = "watch_store";  
$conn = new mysqli($hostname, $username, $password, $dbname);  

if ($conn->connect_error) 
{  
    die("Connection failed: " . $conn->connect_error);  
} 
else 
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {   
        // Get the form data
        $name = $_POST["name"];  
        $email = $_POST["email"];  
        $phone = $_POST["phone"];
        $password = $_POST["password"]; 
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $sql = "INSERT INTO customer_table (Full_Name, Email, Phone, Password) VALUES ('$name', '$email', '$phone', '$hashed_password')";  
        if ($conn->query($sql) === TRUE) 
        {  
            echo "<script>alert('Customer registered successfully.');</script>";
        } 
        else 
        {  
            echo "<script>alert('ERROR: " . $conn->error . "');</script>";
        }  
    } 
}
$conn->close();

?>