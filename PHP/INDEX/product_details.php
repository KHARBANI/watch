<?php
session_start(); // Start the session
$isLoggedIn = isset($_SESSION['user_id']); // Check if the user is logged in
$userName = $isLoggedIn ? $_SESSION['user_name'] : null; // Get the user's name if logged in

// Check if a product ID is passed in the query string
if (isset($_GET['watch_id'])) {
    $product_id = $_GET['watch_id'];

    // Database connection
    $conn = new mysqli("localhost", "root", "", "watch_store");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch product details
    $sql = "SELECT 
                p.Watch_ID, 
                p.Model_Name, 
                p.Price, 
                p.Product_Description AS Description, 
                p.Stock_Quantity, 
                i.Image_URL, 
                c.Category_Name, 
                cm.Case_Name, 
                m.Movement_Name, 
                s.Strap_Name 
            FROM product_table p
            JOIN image_table i ON p.Image_ID = i.Image_ID
            JOIN category_table c ON p.Category_ID = c.Category_ID
            JOIN case_material_table cm ON p.Case_ID = cm.Case_ID
            JOIN movement_table m ON p.Movement_ID = m.Movement_ID
            JOIN strap_material_table s ON p.Strap_ID = s.Strap_ID
            WHERE p.Watch_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "Product not found.";
        exit;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "No product selected.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Page</title>
    <link rel="stylesheet" href="../../CSS/index.css">
    <style>
        /* Product Container */
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        /* Product Image */
        #product-image {
            width: 100%;
            height: auto;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }

        #product-image:hover {
            transform: scale(1.05);
        }

        /* Product Details */
        #product-title {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        #product-price {
            font-size: 1.5rem;
            color: #ff5722;
            margin-bottom: 20px;
        }

        /* Modern Buy Now Button */
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

        /* Product Description */
        .product-description {
            margin-top: 20px;
        }

        .description-part p {
            font-size: 1.2rem;
            color: #555;
            line-height: 1.6;
        }

        .specifications-part {
            margin-top: 10px;
            max-height: auto;
            overflow: hidden;
            transition: max-height 0.5s ease;
        }

        .specifications-part.active {
            max-height: 500px; /* Adjust based on content */
        }

        .see-more {
            margin-top: 10px;
            padding: 10px 20px;
            font-size: 1rem;
            color: white;
            background-color: #333;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .see-more:hover {
            background-color: #555;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
            }
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
    <!-- Header Section -->
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
                    <a onclick="openLoginPopup()">Login</a>
                    <a onclick="openRegisterPopup()">Register</a>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Product Section -->
    <div class="container">
        <img id="product-image" src="/uploads/<?php echo basename($product['Image_URL']); ?>" alt="<?php echo $product['Model_Name']; ?>">
        <div>
            <h1 id="product-title"><?php echo $product['Model_Name']; ?></h1>
            <p id="product-price">₹<?php echo number_format($product['Price'], 2); ?></p>
            <div id="product-description" class="product-description">
                <div class="description-part">
                    <p><?php echo $product['Description']; ?></p>
                </div>
            </div>
            <p><strong>Category:</strong> <?php echo $product['Category_Name']; ?></p>
            <p><strong>Case Material:</strong> <?php echo $product['Case_Name']; ?></p>
            <p><strong>Movement:</strong> <?php echo $product['Movement_Name']; ?></p>
            <p><strong>Strap:</strong> <?php echo $product['Strap_Name']; ?></p>
            <?php if ($product['Stock_Quantity'] == 0): ?>
                <p><strong style="color: red;">Out of Stock</strong></p>
            <?php endif; ?>
            <button type="button" 
                <?php echo $product['Stock_Quantity'] == 0 ? 'disabled' : ''; ?> 
                onclick="handleBuyNow()">
                Buy Now
            </button>
        </div>
    </div>

    <!-- Footer Section -->
    <footer>
        <div class="footer-content">
            <h2 style="text-align: center;">Watch Store</h2>
            <p style="text-align: center;"> 2024 Watch Store. All rights reserved.</p>
            <div class="quick-links">
                <a href="../HTML/index.html">Home</a>
                <a href="../HTML/about.html">About Us</a>
                <a href="../HTML/contact.html">Contact</a>
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
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        // Toggle Specifications
        const seeMoreButton = document.getElementById('see-more');
        const specificationsPart = document.querySelector('.specifications-part');

        if (seeMoreButton) { // Check if the element exists
            seeMoreButton.addEventListener('click', () => {
                specificationsPart.classList.toggle('active');
                seeMoreButton.textContent = specificationsPart.classList.contains('active') ? 'See Less' : 'See More';
            });
        }

        async function handleBuyNow() {
            const isLoggedIn = <?php echo json_encode(isset($_SESSION['user_id'])); ?>;
            if (!isLoggedIn) {
                // Open registration popup
                openRegisterPopup();
            } else {
                try {
                    // Fetch the order ID from the server
                    const response = await fetch('../razorpay/create_order.php', { 
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            amount: <?php echo $product['Price'] * 100; ?>,
                            currency: 'INR',
                            watch_id: <?php echo $product['Watch_ID']; ?>
                        }),
                    });


                    const data = await response.json();
                    if (!data.success) {
                        alert('Failed to create order. Please try again.');
                        return;
                    }

                    const options = {
                        "key": "rzp_test_QoxUlzfLT9H8al", // Razorpay test key ID
                        "amount": <?php echo $product['Price'] * 100; ?>, // Amount in paise
                        "currency": "INR",
                        "name": "SRS Watchstore",
                        "description": "Purchase of <?php echo $product['Model_Name']; ?>",
                        "order_id": data.order_id, // Dynamic order ID from server
                        "handler": function (response) {
                            // Send payment details to the server for verification
                            fetch('../../razorpay/verify_payment.php', { // Updated path
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({
                                    razorpay_payment_id: response.razorpay_payment_id,
                                    razorpay_order_id: response.razorpay_order_id,
                                    razorpay_signature: response.razorpay_signature,
                                    watch_id: <?php echo $product['Watch_ID']; ?>
                                }),
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    window.location.href = "order_success.php?payment_id=" + response.razorpay_payment_id + "&watch_id=<?php echo $product['Watch_ID']; ?>";
                                } else {
                                    alert('Payment verification failed. Please contact support.');
                                    window.location.href = "payment_failure.php?watch_id=<?php echo $product['Watch_ID']; ?>";
                                }
                            })
                            .catch(err => {
                                alert('An error occurred during payment verification. Please try again.');
                            });
                        },
                        "prefill": {
                            "name": "<?php echo htmlspecialchars($userName ?? ''); ?>",
                            "email": "<?php echo isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : 'example@example.com'; ?>",
                            "contact": "<?php echo isset($_SESSION['user_phone']) ? htmlspecialchars($_SESSION['user_phone']) : '1234567890'; ?>"
                        },
                        "theme": {
                            "color": "#3399cc"
                        }
                    };

                    const rzp = new Razorpay(options);
                    rzp.open();
                } catch (error) {
                    alert('An error occurred while processing your request. Please try again.');
                }
            }
        }

        function openRegisterPopup() {
            const registerPopup = document.getElementById('registerPopup');
            registerPopup.style.display = 'block';
        }
    </script>
    <script src="../../JAVA SCRIPT/index.js"></script> 
</body>
</html>
