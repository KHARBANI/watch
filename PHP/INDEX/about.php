<?php
session_start(); // Start the session
$isLoggedIn = isset($_SESSION['user_id']); // Check if the user is logged in
$userName = $isLoggedIn ? $_SESSION['user_name'] : null; // Get the user's name if logged in
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Watch Store</title>
    <link rel="stylesheet" href="../../CSS/index.css">
    <style>
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
        /* Button Styles */
        button {
            padding: 12px 25px;
            font-size: 1rem;
            font-weight: bold;
            color: white;
            background: linear-gradient(90deg, #6a11cb, #2575fc);
            border: none;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        button:hover {
            background: linear-gradient(90deg, #2575fc, #6a11cb);
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }

        button:disabled {
            background: #ccc;
            cursor: not-allowed;
            box-shadow: none;
        }
    </style>
</head>
<body>
    <!-- Navigation panel -->
    <header>
        <h1><a href="index.php">SRS WATCHSTORE</a></h1>
    </header>
    <nav>
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <div class="dropdown">
            <a>Shop</a>
            <div class="dropdown-content">
                <a href="brand.php">Brand</a>
                <a href="all_product.php">All Product</a>
            </div>
        </div>
        <div class="dropdown">
            <?php if ($isLoggedIn): ?>
                <a>Access <?php echo htmlspecialchars($userName); ?></a>
                <div class="dropdown-content">
                    <a href="my_account.php">My Account</a>
                    <a href="logout.php">Logout</a>
                </div>
            <?php else: ?>
                <a>Access</a>
                <div class="dropdown-content">
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <!-- About Section -->
    <section class="about">
        <h2 style="text-align: center;">About Us</h2>
        <p style="margin-left: 100px; margin-right: 100px; text-align: justify; margin-top: 50px;">
            At SRS Watchstore, we create watches that combine timeless design with exceptional quality.Our goal is 
            to offer stylish and reliable timepieces that complement your individuality and fit seamlessly into your lifestyle.
        </p>
        <p style="margin-left: 100px; margin-right: 100px; text-align: justify; margin-bottom: 100px;">
            Every watch in our collection is crafted with precision and care, ensuring a perfect blend of elegance 
            and functionality. Whether you're dressing up for a special occasion or adding a touch of sophistication 
            to your everyday look, SRS Watchstore has the perfect watch for you.
            Discover the art of timekeeping with SRS Watchstore—where every moment matters.
        </p>
    </section>
    <div style="margin-top: 190px;">
        <pre> </pre>
    </div>

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