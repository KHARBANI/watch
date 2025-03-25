<?php
include 'db_connection.php';

// Get the date 30 days ago
$date30DaysAgo = date('Y-m-d', strtotime('-30 days'));

// Fetch Total Sales (Last 30 Days)
$sqlTotalSales = "SELECT SUM(Total_Price) AS total_sales 
                  FROM order_table 
                  WHERE Order_Date >= '$date30DaysAgo'";
$resultTotalSales = $conn->query($sqlTotalSales);
$totalSales = $resultTotalSales->fetch_assoc()['total_sales'] ?? 0;

// Fetch Total Products (No change needed here as it's not time-based)
$sqlTotalProducts = "SELECT COUNT(*) AS total_products FROM product_table";
$resultTotalProducts = $conn->query($sqlTotalProducts);
$totalProducts = $resultTotalProducts->fetch_assoc()['total_products'] ?? 0;

// Fetch Total Orders (Last 30 Days)
$sqlTotalOrders = "SELECT COUNT(*) AS total_orders 
                   FROM order_table 
                   WHERE Order_Date >= '$date30DaysAgo'";
$resultTotalOrders = $conn->query($sqlTotalOrders);
$totalOrders = $resultTotalOrders->fetch_assoc()['total_orders'] ?? 0;

// Fetch Total Reviews (Last 30 Days)
$sqlTotalReviews = "SELECT COUNT(*) AS total_reviews 
                    FROM review_table 
                    WHERE Review_Date >= '$date30DaysAgo'";
$resultTotalReviews = $conn->query($sqlTotalReviews);
$totalReviews = $resultTotalReviews->fetch_assoc()['total_reviews'] ?? 0;

// Fetch Latest Orders (Last 30 Days)
$sqlLatestOrders = "SELECT o.Order_ID, p.Model_Name, o.Total_Price, o.Order_Date 
                     FROM order_table o
                     JOIN product_table p ON o.Watch_ID = p.Watch_ID
                     WHERE o.Order_Date >= '$date30DaysAgo'
                     ORDER BY o.Order_Date DESC LIMIT 5";
$resultLatestOrders = $conn->query($sqlLatestOrders);

// Fetch Recent Payments (Last 30 Days)
$sqlRecentPayments = "SELECT Payment_ID, Amount, Payment_Date 
                       FROM payment_table 
                       WHERE Payment_Date >= '$date30DaysAgo'
                       ORDER BY Payment_Date DESC LIMIT 5";
$resultRecentPayments = $conn->query($sqlRecentPayments);

// Fetch Recent Reviews (Last 30 Days)
$sqlRecentReviews = "SELECT c.Full_Name, p.Model_Name, r.Review_Details, r.Review_Date 
                      FROM review_table r
                      JOIN customer_table c ON r.Customer_ID = c.Customer_ID
                      JOIN product_table p ON r.Order_ID = p.Watch_ID
                      WHERE r.Review_Date >= '$date30DaysAgo'
                      ORDER BY r.Review_Date DESC LIMIT 5";
$resultRecentReviews = $conn->query($sqlRecentReviews);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dealer Dashboard</title>
    <link rel="stylesheet" href="../CSS/dealer_dashboard.css">
    <script src="../JAVA SCRIPT/test.js"></script>
</head>
<body>
    <!-- Navigation panel -->
    <header>
        <h1><a href="dealer_dashboard.php">DEALER DASHBOARD</a></h1>
    </header>
    <nav>
        <a href="dealer_dashboard.php">Dashboard</a>
        <a href="dealer_manage_products.php">Manage Product</a>
        <a href="dealer_view_categories.php">View Category</a>
        <a href="dealer_manage_orders.php">Manage Order</a>
        <a href="dealer_view_payments.php">View Payment</a>
        <a href="dealer_view_reviews.php">View Review</a>
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
                        <?php while ($row = $resultLatestOrders->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['Order_ID']; ?></td>
                            <td><?php echo $row['Model_Name']; ?></td>
                            <td>Pending</td>
                            <td><?php echo $row['Order_Date']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
                <div class="activity-card">
                    <h3>Recent Payments</h3>
                    <table>
                        <tr>
                            <th>Payment Id</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                        <?php while ($row = $resultRecentPayments->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['Payment_ID']; ?></td>
                            <td><?php echo $row['Amount']; ?></td>
                            <td><?php echo $row['Payment_Date']; ?></td>
                        </tr>
                        <?php endwhile; ?>
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
                        <?php while ($row = $resultRecentReviews->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['Full_Name']; ?></td>
                            <td><?php echo $row['Model_Name']; ?></td>
                            <td>5</td>
                            <td><?php echo $row['Review_Details']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer Section -->
    <footer>
        <p>&copy; 2024 Dealer Dashboard. All rights reserved.</p>
    </footer>
</body>
</html>