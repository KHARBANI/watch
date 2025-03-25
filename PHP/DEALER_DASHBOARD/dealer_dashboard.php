<?php
session_start();
if (!isset($_SESSION['dealer_id'])) {
    echo "<script>showPopupMessage('Please login first.', 'error');</script>";
    echo "<script>window.location.href = '../../PHP/INDEX/login.php';</script>";
    exit();
}

require_once '../../PHP/db_connection.php';

$dealer_id = $_SESSION['dealer_id'];
$sql = "SELECT * FROM dealer_table WHERE Dealer_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $dealerDetails = $result->fetch_assoc();
    // Display dealer details
    echo "<script>showPopupMessage('Welcome, " . $dealerDetails['Dealer_Name'] . "', 'success');</script>";
    // ...display other dealer details...
} else {
    echo "<script>showPopupMessage('No details found for this dealer.', 'error');</script>";
}

$stmt->close();

// Fetch total products based on dealer ID
$totalProductsQuery = "SELECT COUNT(*) as totalProducts FROM product_table WHERE Dealer_ID = ?";
$stmt = $conn->prepare($totalProductsQuery);
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$totalProductsResult = $stmt->get_result();
$totalProductsRow = $totalProductsResult->fetch_assoc();
$totalProducts = $totalProductsRow['totalProducts'];

$stmt->close();

// Fetch total orders based on dealer ID
$totalOrdersQuery = "
    SELECT COUNT(od.Order_ID) as totalOrders
    FROM product_table p
    JOIN order_detail_table od ON p.Watch_ID = od.Watch_ID
    JOIN order_table o ON od.Order_ID = o.Order_ID
    WHERE p.Dealer_ID = ?
";
$stmt = $conn->prepare($totalOrdersQuery);
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$totalOrdersResult = $stmt->get_result();
$totalOrdersRow = $totalOrdersResult->fetch_assoc();
$totalOrders = $totalOrdersRow['totalOrders'];

$stmt->close();

// Fetch data for overview cards
$totalSalesQuery = "
    SELECT SUM(pmt.Amount) as totalSales
    FROM payment_table pmt
    JOIN order_table o ON pmt.Order_ID = o.Order_ID
    JOIN order_detail_table od ON o.Order_ID = od.Order_ID
    JOIN product_table p ON od.Watch_ID = p.Watch_ID
    WHERE p.Dealer_ID = ?
";
$stmt = $conn->prepare($totalSalesQuery);
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$totalSalesResult = $stmt->get_result();
$totalSalesRow = $totalSalesResult->fetch_assoc();
$totalSales = $totalSalesRow['totalSales'];

$stmt->close();

$totalReviewsQuery = "
    SELECT COUNT(r.Review_ID) as totalReviews
    FROM review_table r
    JOIN product_table p ON r.Watch_ID = p.Watch_ID
    WHERE p.Dealer_ID = ?
";
$stmt = $conn->prepare($totalReviewsQuery);
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$totalReviewsResult = $stmt->get_result();
$totalReviewsRow = $totalReviewsResult->fetch_assoc();
$totalReviews = $totalReviewsRow['totalReviews'];

$stmt->close();

// Fetch recent orders (last 30 days)
$recentOrdersQuery = "
    SELECT o.Order_ID, p.Model_Name, o.Order_Status, o.Order_Date
    FROM order_table o
    JOIN order_detail_table od ON o.Order_ID = od.Order_ID
    JOIN product_table p ON od.Watch_ID = p.Watch_ID
    WHERE p.Dealer_ID = ? AND o.Order_Date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    ORDER BY o.Order_Date DESC
";
$stmt = $conn->prepare($recentOrdersQuery);
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$recentOrdersResult = $stmt->get_result();
$recentOrders = $recentOrdersResult->fetch_all(MYSQLI_ASSOC);

$stmt->close();

// Fetch recent payments (last 30 days)
$recentPaymentsQuery = "
    SELECT pmt.Payment_ID, pmt.Order_ID, pmt.Amount, pmt.Payment_Date
    FROM payment_table pmt
    JOIN order_table o ON pmt.Order_ID = o.Order_ID
    JOIN order_detail_table od ON o.Order_ID = od.Order_ID
    JOIN product_table p ON od.Watch_ID = p.Watch_ID
    WHERE p.Dealer_ID = ? AND pmt.Payment_Date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    ORDER BY pmt.Payment_Date DESC
";
$stmt = $conn->prepare($recentPaymentsQuery);
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$recentPaymentsResult = $stmt->get_result();
$recentPayments = $recentPaymentsResult->fetch_all(MYSQLI_ASSOC);

$stmt->close();

// Fetch recent reviews
$recentReviewsQuery = "
    SELECT c.Customer_Name as customer, p.Model_Name as product, r.Rating as rating, r.Review_Details as comment
    FROM review_table r
    JOIN product_table p ON r.Watch_ID = p.Watch_ID
    JOIN customer_table c ON r.Customer_ID = c.Customer_ID
    WHERE p.Dealer_ID = ?
    ORDER BY r.Review_Date DESC
";
$stmt = $conn->prepare($recentReviewsQuery);
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$recentReviewsResult = $stmt->get_result();
$recentReviews = $recentReviewsResult->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dealer Dashboard</title>
    <link rel="stylesheet" href="../../CSS/dealer_dashboard.css">
    <style>
        .activity-card {
            width: 300px; /* Fixed width */
            /* Remove fixed height */
            overflow-y: auto; /* Scroll if content overflows */
        }
        #popupMessage {
            position: fixed;
            top: 200px;
            left: 700px;
            padding: 15px;
            border-radius: 5px;
            color: #fff;
            font-size: 14px;
            display: none;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        #popupMessage.success {
            background-color: #28a745;
        }
        #popupMessage.error {
            background-color: #dc3545;
        }
    </style>
    <script>
        function showPopupMessage(message, type) {
            var popup = document.getElementById('popupMessage');
            popup.innerText = message;
            popup.className = type;
            popup.style.display = 'block';
            setTimeout(function() {
                popup.style.display = 'none';
            }, 3000);
        }
    </script>
</head>
<body>
    <!-- Navigation panel -->
    <header>
        <h1><a href="dealer_dashboard.html">DEALER DASHBOARD</a></h1>
    </header>
    <nav>
        <a href="../../PHP/DEALER_DASHBOARD/dealer_dashboard.php">Dashboard</a>
        <div class="dropdown-setting">
            <a>Product/Stock</a>
            <div class="dropdown-content-setting">
                <a href="../../PHP/DEALER_MANAGE_PRODUCT/dealer_manage_stock.php">Manage Stock</a>
                <a href="../../PHP/DEALER_MANAGE_PRODUCT/dealer_manage_products.php">Manage Product</a>
            </div>
        </div>
        <a href="../../PHP/DEALER_VIEW_CATEGORY/dealer_view_categories.php">View Category</a>
        <a href="../../PHP/DEALER_MANAGE_ORDER/dealer_manage_orders.php">Manage Order</a>
        <a href="../../PHP/DEALER_VIEW_PAYMENT/dealer_view_payments.php">View Payment</a>
        <a href="../../PHP/DEALER_VIEW_REVIEW/dealer_view_reviews.php">View Review</a>
        <div class="dropdown-setting">
            <a>Access</a>
            <div class="dropdown-content-setting">
                <a href="../PHP/logout.php">Logout</a>
            </div>
        </div>  
    </nav>

    <!-- Main Content Section -->
    <main>
        <section class="dashboard-overview">
            <h2>Overview</h2>
            <div class="overview-cards">
                <div class="card">
                    <h3>Total Sales</h3>
                    <p><?php echo $totalSales; ?></p>
                </div>
                <div class="card">
                    <h3>Total Products</h3>
                    <p><?php echo $totalProducts; ?></p>
                </div>
                <div class="card">
                    <h3>Total Orders</h3>
                    <p><?php echo $totalOrders; ?></p>
                </div>
                <div class="card">
                    <h3>Reviews</h3>
                    <p><?php echo $totalReviews; ?></p>
                </div>
            </div>
        </section>

        <!-- Modern Recent Activity Section -->
        <section class="recent-activity">
            <h2>Recent Activity</h2>
            <div class="activity-cards">
                <div class="activity-card">
                    <h3>Latest Orders</h3>
                    <table>
                        <tr>
                            <th>Order Id</th>
                            <th>Product</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                        <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td><?php echo $order['Order_ID']; ?></td>
                            <td><?php echo $order['Model_Name']; ?></td>
                            <td><?php echo $order['Order_Status']; ?></td>
                            <td><?php echo $order['Order_Date']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <div class="activity-card">
                    <h3>Recent Payments</h3>
                    <table>
                        <tr>
                            <th>Payment Id</th>
                            <th>Order Id</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                        <?php foreach ($recentPayments as $payment): ?>
                        <tr>
                            <td><?php echo $payment['Payment_ID']; ?></td>
                            <td><?php echo $payment['Order_ID']; ?></td>
                            <td><?php echo $payment['Amount']; ?></td>
                            <td><?php echo $payment['Payment_Date']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <div class="activity-card">
                    <h3>Recent Reviews</h3>
                    <table>
                        <tr>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Rating</th>
                            <th>Comment</th>
                        </tr>
                        <?php foreach ($recentReviews as $review): ?>
                        <tr>
                            <td><?php echo $review['customer']; ?></td>
                            <td><?php echo $review['product']; ?></td>
                            <td><?php echo $review['rating']; ?></td>
                            <td><?php echo $review['comment']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer Section -->
    <footer>
        <p>&copy; 2024 Dealer Dashboard. All rights reserved.</p>
    </footer>

    <!-- Popup Message -->
    <div id="popupMessage"></div>
</body>
</html>