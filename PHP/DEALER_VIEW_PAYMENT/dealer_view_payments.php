<?php
// Database connection
require_once('../../PHP/db_connection.php');

session_start();

if (isset($_SESSION['dealer_id'])) {
    $dealer_id = intval($_SESSION['dealer_id']);

    // Fetch payment data with product name and customer name
    $sql = "SELECT p.Payment_ID, c.Customer_Name, pp.Model_Name, p.Amount, p.Payment_Date 
            FROM payment_table p
            JOIN order_detail_table od ON p.Order_ID = od.Order_ID
            JOIN product_table pp ON od.Watch_ID = pp.Watch_ID
            JOIN order_table o ON p.Order_ID = o.Order_ID
            JOIN customer_table c ON o.Customer_ID = c.Customer_ID
            WHERE pp.Dealer_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $dealer_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    echo "<p>Dealer not logged in.</p>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dealer Dashboard</title>
    <link rel="stylesheet" href="../../CSS/dealer_dashboard.css">
    <script src="../JAVA SCRIPT/dealer_view_payment.js"></script>
    <style>
        /* Modal Styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Navigation panel -->
    <header>
        <h1><a href="dealer_dashboard.html">DEALER DASHBOARD</a></h1>
    </header>
    <nav>
        <a href="../../PHP/DEALER_DASHBOARD/dealer_dashboard.php">Dashboard</a>
        <a href="../../PHP/DEALER_MANAGE_PRODUCT/dealer_manage_products.php">Manage Product</a>
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
    
    <main>
        <section class="manage-payments">
            <h2>View Payments</h2>
            <table>
                <thead>
                    <tr>
                        <th>Payment ID</th>
                        <th>Customer Name</th>
                        <th>Product Name</th>
                        <th>Amount</th>
                        <th>Payment Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="paymentTableBody">
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['Payment_ID']}</td>
                                    <td>{$row['Customer_Name']}</td>
                                    <td>{$row['Model_Name']}</td>
                                    <td>{$row['Amount']}</td>
                                    <td>{$row['Payment_Date']}</td>
                                    <td>
                                        <button class='action-btn view-btn' onclick='viewPayment({$row['Payment_ID']})'>View</button>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No payments found</td></tr>";
                    }

                    $stmt->close();
                    ?>
                </tbody>
            </table>
        </section>
    </main>

    <!-- Footer Section -->
    <footer>
        <p>&copy; 2024 Dealer Dashboard. All rights reserved.</p>
    </footer>

    <!-- Modal for Product information -->
    <div id="modal" class="modal" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
        <div class="modal-content">
            <span class="close" onclick="closeInfoModal()" aria-label="Close modal">&times;</span>
            <div id="modalContent"></div>
        </div>
    </div>

    <script>
        function viewPayment(paymentId) {
            // Fetch payment details using AJAX
            fetch(`get_payment_details.php?payment_id=${paymentId}`)
                .then(response => response.json())
                .then(data => {
                    // Populate modal with payment details
                    const modalContent = document.getElementById('modalContent');
                    modalContent.innerHTML = `
                        <h2>Payment Details</h2>
                        <p><strong>Payment ID:</strong> ${data.Payment_ID}</p>
                        <p><strong>Order ID:</strong> ${data.Order_ID}</p>
                        <p><strong>Customer Name:</strong> ${data.Customer_Name}</p>
                        <p><strong>Product Name:</strong> ${data.Model_Name}</p>
                        <p><strong>Amount:</strong> ${data.Amount}</p>
                        <p><strong>Payment Date:</strong> ${data.Payment_Date}</p>
                        <p><strong>Payment Time:</strong> ${data.Payment_Time}</p>
                        <p><strong>Payment Method:</strong> ${data.Payment_Method}</p>
                    `;
                    // Display the modal
                    document.getElementById('modal').style.display = 'block';
                })
                .catch(error => console.error('Error fetching payment details:', error));
        }

        function closeInfoModal() {
            document.getElementById('modal').style.display = 'none';
        }
    </script>
</body>
</html>
