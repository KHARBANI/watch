<?php
require_once 'db_connection.php';

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch total counts for overview cards
$totalCustomers = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM customer_table"))[0];
$totalDealers = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM dealer_table"))[0];
$totalUsers = $totalCustomers + $totalDealers; // Combine customers and dealers
$totalCategories = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM category_table"))[0];
$totalProducts = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM product_table"))[0];
$totalOrders = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(DISTINCT Order_ID) FROM payment_table"))[0];

// Fetch recently added products in the last 30 days
$recentProductsQuery = "
    SELECT p.Watch_ID, p.Model_Name, p.Price, c.Category_Name 
    FROM product_table p
    INNER JOIN category_table c ON p.Category_ID = c.Category_ID
    WHERE p.Created_At >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    ORDER BY p.Created_At DESC 
    LIMIT 5
";
$recentProductsResult = mysqli_query($conn, $recentProductsQuery);
$recentProducts = [];
while ($row = mysqli_fetch_assoc($recentProductsResult)) {
    $recentProducts[] = $row;
}

// Fetch recent payments in the last 30 days
$recentPaymentsQuery = "
    SELECT Payment_ID, Order_ID, Payment_Method, Amount, Payment_Date 
    FROM payment_table 
    WHERE Payment_Date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    ORDER BY Payment_Date DESC 
    LIMIT 5
";
$recentPaymentsResult = mysqli_query($conn, $recentPaymentsQuery);
$recentPayments = [];
while ($row = mysqli_fetch_assoc($recentPaymentsResult)) {
    $recentPayments[] = $row;
}

// Fetch recent reviews in the last 30 days without joins
$recentReviewsQuery = "
    SELECT Review_ID, Review_Details, Customer_ID, Review_Date, Rating, Order_ID, Watch_ID
    FROM review_table
    WHERE Review_Date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    ORDER BY Review_Date DESC 
    LIMIT 5
";
$recentReviewsResult = mysqli_query($conn, $recentReviewsQuery);
if (!$recentReviewsResult) {
    die("Query failed: " . mysqli_error($conn));
}

$recentReviews = [];
while ($row = mysqli_fetch_assoc($recentReviewsResult)) {
    $recentReviews[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../CSS/admin_dashboard.css">
    <style>
        .activity-card {
            width: 30%;
            margin: 1%;
            box-sizing: border-box;
        }
        .activity-cards {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <!-- Navigation panel -->
    <header>
        <h1><a href="../admin_dashboard/admin_dashboard.php">ADMIN DASHBOARD</a></h1>
    </header>
    <nav>
        <a href="../admin_dashboard/admin_dashboard.php">Dashboard</a>
        <div class="dropdown-setting">
            <a href="#settings">Manage Users</a>
            <div class="dropdown-content-setting">
                <a href="../admin_manage_customer/admin_manage_customer.php">Customer</a>
                <a href="../admin_manage_dealer/admin_manage_dealer.php">Dealer</a>
            </div>
        </div>
        <a href="../admin_manage_category/admin_manage_category.php">Manage Category</a>
        <a href="../admin_view_product/admin_view_products.php">View Products</a>
        <a href="../admin_view_payment/admin_view_payments.php">View Payments</a>
        <a href="../admin_view_order/admin_view_orders.php">View Orders</a>
        <a href="../admin_view_review/admin_view_reviews.php">View Reviews</a>
        <div class="dropdown-setting">
            <a href="#settings">Settings</a>
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
                    <h3>Total Users</h3>
                    <p><?php echo $totalUsers; ?></p>
                </div>
                <div class="card">
                    <h3>Total Categories</h3>
                    <p><?php echo $totalCategories; ?></p>
                </div>
                <div class="card">
                    <h3>Total Products</h3>
                    <p><?php echo $totalProducts; ?></p>
                </div>
                <div class="card">
                    <h3>Total Orders</h3>
                    <p><?php echo $totalOrders; ?></p>
                </div>
            </div>
        </section>
        <!-- Modern Recent Activity Section -->
        <section class="recent-activity">
            <h2>Recent Activity (Last 30 Days)</h2>
            <div class="activity-cards">
                <div class="activity-card">
                    <h3>Recently Added Products</h3>
                    <table>
                        <tr>
                            <th>Product ID</th>
                            <th>Model Name</th>
                            <th>Price</th>
                            <th>Category</th>
                        </tr>
                        <?php foreach ($recentProducts as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['Watch_ID']); ?></td>
                            <td><?php echo htmlspecialchars($product['Model_Name']); ?></td>
                            <td><?php echo htmlspecialchars($product['Price']); ?></td>
                            <td><?php echo htmlspecialchars($product['Category_Name']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <div class="activity-card">
                    <h3>Recent Payments</h3>
                    <table>
                        <tr>
                            <th>Payment ID</th>
                            <th>Order ID</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                        <?php foreach ($recentPayments as $payment): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($payment['Payment_ID']); ?></td>
                            <td><?php echo htmlspecialchars($payment['Order_ID']); ?></td>
                            <td><?php echo htmlspecialchars($payment['Amount']); ?></td>
                            <td><?php echo htmlspecialchars($payment['Payment_Date']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <div class="activity-card">
                    <h3>Recent Reviews</h3>
                    <table>
                        <tr>
                            <th>Review ID</th>
                            <th>Customer ID</th>
                            <th>Watch ID</th>
                            <th>Rating</th>
                            <th>Date</th>
                        </tr>
                        <?php if (empty($recentReviews)): ?>
                        <tr>
                            <td colspan="7">No recent reviews found.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($recentReviews as $review): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($review['Review_ID']); ?></td>
                            <td><?php echo htmlspecialchars($review['Customer_ID']); ?></td>
                            <td><?php echo htmlspecialchars($review['Watch_ID']); ?></td>
                            <td><?php echo htmlspecialchars($review['Rating']); ?></td>
                            <td><?php echo htmlspecialchars($review['Review_Date']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </section>
    </main>
    <!-- Footer Section -->
    <footer>
        <p>&copy; 2024 Admin Dashboard. All rights reserved.</p>
    </footer>
</body>
</html>