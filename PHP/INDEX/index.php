<?php
// Database connection
require_once '../../PHP/db_connection.php';

// Fetch top three best seller products based on highest sales, prioritizing the most recently purchased
$sql = "SELECT p.Watch_ID, p.Model_Name, p.Price, i.Image_URL 
        FROM product_table p 
        JOIN image_table i ON p.Image_ID = i.Image_ID 
        JOIN order_detail_table od ON p.Watch_ID = od.Watch_ID 
        JOIN order_table o ON od.Order_ID = o.Order_ID 
        GROUP BY p.Watch_ID 
        ORDER BY SUM(od.Quantity) DESC, MAX(o.Order_Date) DESC, MAX(o.Order_Time) DESC 
        LIMIT 3";
$result = $conn->query($sql);

// Fetch top three most recently added products
$newArrivalsSql = "SELECT p.Watch_ID, p.Model_Name, p.Price, i.Image_URL 
                   FROM product_table p 
                   JOIN image_table i ON p.Image_ID = i.Image_ID 
                   ORDER BY p.Created_At DESC 
                   LIMIT 3";
$newArrivalsResult = $conn->query($newArrivalsSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>
    <link rel="stylesheet" href="../../CSS/index.css">
    <style>
        .new-arrivals {
        background-color: #fff;
        padding: 20px;
        text-align: center;
        }

        .new-arrivals h2 {
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .new-arrivals-items {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }

        .item {
            background-color: #f4f4f4;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            margin: 10px;
            width: 30%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .item img {
            max-width: 100%;
            height: 500px;
            border-radius: 5px;
            object-fit: cover;
            width: 400px;
        }

        .item h3 {
            font-size: 1.5rem;
            margin: 10px 0;
        }

        .item p {
            font-size: 1.2rem;
            color: #333;
        }

        select#usertype {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f4f4f4;
            font-size: 1rem;
            color: #333;
            margin-top: 10px;
        }
        select#usertype:focus {
            border-color: #aaa;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <!--Navigation panel-->
    <header>
        <h1><a href="index.html">SRS WATCHSTORE</a></h1>
    </header>
    <nav>
        <a href="index.html">Home</a>
        <a href="about.html">About</a>
        <a href="contact.html">Contact</a>
        <div class="dropdown">
            <a>Shop</a>
            <div class="dropdown-content">
                <!--<a href="#men">Men</a>
                <a href="#women">Women</a>
                <a href="#sport">Sport</a>
                <a href="#fashion">Fashion</a>-->
                <a href="../frontend/brand.html">Brand</a>
            </div>
        </div>
        <div class="dropdown">
            <a>Access</a>
            <div class="dropdown-content">
                <a onclick="openLoginPopup()">Login</a>
                <a onclick="openRegisterPopup()">Register</a>
            </div>
        </div>
    </nav>
 
   <!-- Slide featured display -->
   <section class="slider">
    <div class="slides">
        <div class="slide active">
            <img src="../../IMAGES/Featured_display(1).jpeg" alt="Featured Watch 1">
            <div class="description">
                <h3>Mugnier</h3>
                <p>The Mugnier timepiece is a refined watch known for its elegance and meticulous craftsmanship, embodying timeless sophistication and precision.</p>
            </div>
        </div>
        <div class="slide">
            <img src="../../IMAGES/Featured_display(2).jpeg" alt="Featured Watch 2">
            <div class="description">
                <h3>Omega Speedmaster</h3>
                <p>The Omega Speedmaster is famous for its role in space exploration and is a symbol of innovation.</p>
            </div>
        </div>
        <div class="slide">
            <img src="../../IMAGES/Featured_display(3).jpeg" alt="Featured Watch 3">
            <div class="description">
                <h3>Rolex</h3>
                <p>The Rolex Submarine is known for its durability and precision.</p>
            </div>
        </div>
    </div>
    <button class="prev" onclick="changeSlide(-1)">&#10094;</button>
    <button class="next" onclick="changeSlide(1)">&#10095;</button>
</section>

<!--Best Seller Section-->
<section class="best-seller">
    <h2>Best Sellers</h2>
    <div class="best-seller-items">
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $imagePath = basename($row["Image_URL"]);
                echo '<div class="item">';
                echo '<img src="/uploads/' . $imagePath . '" alt="Best Seller Watch">';
                echo '<h3>' . $row["Model_Name"] . '</h3>';
                echo '<p>Price: $' . $row["Price"] . '</p>';
                echo '<button class="buy-button" onclick="window.location.href=\'../frontend/' . strtolower(str_replace(' ', '', $row["Model_Name"])) . '.html\'">Buy Now</button>';
                echo '</div>';
            }
        } else {
            echo "No best sellers available.";
        }
        ?>
    </div>
</section>

<!--Popular Product Section-->
<section class="new-arrivals">
    <h2>New Arrivals</h2>
    <div class="new-arrivals-items">
        <?php
        if ($newArrivalsResult->num_rows > 0) {
            while($row = $newArrivalsResult->fetch_assoc()) {
                $imagePath = basename($row["Image_URL"]);
                echo '<div class="item">';
                echo '<img src="/uploads/' . $imagePath . '" alt="New Arrival Watch">';
                echo '<h3>' . $row["Model_Name"] . '</h3>';
                echo '<p>Price: $' . $row["Price"] . '</p>';
                echo '<button class="buy-button" onclick="window.location.href=\'../frontend/' . strtolower(str_replace(' ', '', $row["Model_Name"])) . '.html\'">Buy Now</button>';
                echo '</div>';
            }
        } else {
            echo "No new arrivals available.";
        }
        ?>
    </div>
</section>

<!--Our Brand Section-->
<section class="our-brand">
    <h2>Our Brands</h2>
    <div class="brand-items">
        <div class="brand-item">
            <a href="../frontend/armani.html">
                <img src="../../IMAGES/armaniexchange-logo_1.jpg" alt="Armani Exchange Logo">
            </a>
         </div>
        <div class="brand-item">
            <a href="../frontend/aigner.html">
                <img src="../../IMAGES/aigner-logo.jpg" alt="Aigner Logo">
            </a>
        </div>
        <div class="brand-item">
            <a href="../frontend/AMAZEFIT.HTML">
                <img src="../../IMAGES/amazfit-logo.jpg" alt="Amazing-fit Logo">
            </a>
        </div>
        <div class="brand-item">
           <a href="../frontend/casio.html">
                <img src="../../IMAGES/casio-logo.jpg" alt="Casio Logo">
           </a>
        </div>
        <div class="brand-item">
            <a href="../frontend/balmain.html">
                <img src="../../IMAGES/balmain-logo.jpg" alt="Balmain Logo">
            </a>
         </div>
    </div>
</section>

<!-- Footer Section -->
<footer>
    <div class="footer-content">
        <h2 style="text-align: center;">Watch Store</h2>
        <p style="text-align: center;"> 2024 Watch Store. All rights reserved.</p>
        <div class="quick-links">
            <a href="index.html">Home</a>
            <a href="about.html">About Us</a>
            <a href="contact.html">Contact</a>
            <a href="#privacy">Privacy Policy</a>
            <a href="#terms">Terms of Service</a>
        </div>
    </div>
    <div style="margin-top: 30px;" class="footer-details">
        <span style="margin-left: 100px;">Contact No:</strong> (+91) 9876543210</span>
        <span style="margin-left: 80px;">|</span>
        <span style="margin-left: 80px;"><strong>Location:</strong>&nbsp;  St. Xavier's College Ahmedabad, Navrangpura, Gujarat - 390009</span>
        <span style="margin-left: 60px;">|</span>
        <span style="margin-left: 80px;"><strong>Email:</strong> <a href="mailto:info@watchstore.com">info@watchstore.com</a></span>
    </div>
    <div style="margin-top: 30px;">
        
    </div>
</footer>

<!-- Modern Login Popup Form -->
<div id="loginPopup" class="login-popup">
    <div class="login-content">
        <h2>Welcome Back</h2>
        <p>Login to your account</p>
        <form action="../../PHP/INDEX/login_auth.php" method="POST" onsubmit="return validateLoginForm()">
            <div class="input-group">
                <label for="loginEmailUnique">Email</label>
                <input type="text" id="loginEmailUnique" name="email" placeholder="Enter your email" autocomplete="email">
                <span id="loginEmailError" class="error-message"></span>
            </div>
            <div class="input-group">
                <label for="loginPasswordUnique">Password</label>
                <input type="password" id="loginPasswordUnique" name="password" placeholder="Enter your password" autocomplete="current-password">
                <span id="loginPasswordError" class="error-message"></span>
            </div>
            <div class="input-group">
                <label for="usertype">User Type</label>
                <select name="usertype" id="usertype">
                    <option value="default" style="text-align:center;">-----------USER TYPE-----------</option>
                    <option value="dealer">Dealer</option>
                    <option value="customer">Customer</option>
                </select>
                <span id="usertypeError" class="error-message"></span>
            </div>
            <p class="forgot-password-link"><a href="#forgot-password" onclick="openForgotPasswordPopup()">Forgot your password?</a></p>
            <div class="button-group">
                <button type="submit" name="login" class="login-btn">Login</button>
                <button type="button" class="cancel-btn" onclick="closeLoginPopup()">Cancel</button>
            </div>
            <p class="signup-link">Don't have an account? <a href="#register" onclick="openRegisterPopup()">Register now</a></p>
        </form>
    </div>
</div>

<!-- Forgot Password Popup Form -->
<div id="forgotPasswordPopup" class="forgot-password-popup">
    <div class="forgot-password-content">
        <h2>Forgot Password</h2>
        <p>Please enter your email address to receive password recovery instructions.</p>
        <form action="../PHP/forgot_password.php" method="POST" onsubmit="return validateForgotPasswordForm()">
            <div class="input-group">
                <label for="forgotPasswordEmail">Email</label>
                <input type="text" id="forgotPasswordEmail" name="email" placeholder="Enter your email" autocomplete="email">
                <span id="forgotPasswordEmailError" class="error-message"></span>
            </div>
            <div class="button-group">
                <button type="submit" class="submit-btn">Send Instructions</button>
                <button type="button" class="cancel-btn" onclick="closeForgotPasswordPopup()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Change Password Popup Form -->
<div id="changePasswordPopup" class="change-password-popup">
    <div class="change-password-content">
        <h2>Change Password</h2>
        <form id="changePasswordForm" action="../PHP/forgot_password.php" method="POST" onsubmit="return validateChangePasswordForm()">
            <div class="input-group">
                <label for="newPassword">New Password:</label>
                <input type="password" id="newPassword" name="password" placeholder="Enter your new password">
                <span id="newPasswordError" class="error-message"></span>
                <label for="confirmPassword">Confirm Password:</label>
                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password">
                <span id="confirmPasswordError" class="error-message"></span>
            </div>
            <div class="button-group">
                <button type="submit" class="submit-btn">Send Instructions</button>
                <button type="button" class="cancel-btn" onclick="closeChangePasswordPopup()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Registration Popup Form -->
<div id="registerPopup" class="register-popup">
    <div class="register-content">
        <h2>Register Now</h2>
        <p>Create a new account</p>
        <form action="../PHP/register.php" method="post">
            <div class="input-group">
                <label for="nameUnique">Name</label>
                <input type="text" id="nameUnique" name="name" placeholder="Enter your Name" autocomplete="name">
                <span id="nameError" class="error-message"></span>
            </div>
            <div class="input-group">
                <label for="registerEmailUnique">Email</label>
                <input type="text" id="registerEmailUnique" name="email" placeholder="Enter your email"  autocomplete="email">
                <span id="registerEmailError" class="error-message"></span>
            </div>
            <div class="input-group">
                <label for="phoneUnique">Phone Number</label>
                <input type="tel" id="phoneUnique" name="phone" placeholder="Enter your phone number" autocomplete="tel">
                <span id="phoneError" class="error-message"></span>
            </div>
            <div class="input-group">
                <label for="registerPasswordUnique">Password</label>
                <input type="password" id="registerPasswordUnique" name="password" placeholder="Enter your password" >
                <span id="registerPasswordError" class="error-message"></span>
            </div>
            <div class="input-group">
                <label for="registerConfirmPasswordUnique">Confirm Password</label>
                <input type="password" id="registerConfirmPasswordUnique" name="confirmPassword" placeholder="Confirm your password">
                <span id="registerConfirmPasswordError" class="error-message"></span>
            </div>
            <div class="button-group">
                <button type="submit" class="register-btn">Register</button>
                <button type="button" class="cancel-btn" onclick="closeRegisterPopup()">Cancel</button>
            </div>
            <p class="signup-link">Already a registered user? <a href="#register" onclick="openLoginPopup()">Login now</a></p>
        </form>
    </div>
</div>
<script src="../../JAVA SCRIPT/index.js"></script>    
</body>
</html>

<!---last updated 12/10/2023-->
