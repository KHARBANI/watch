<?php
// database connection
require_once '../../PHP/db_connection.php';

// Fetch payment data from the database
$query = "
    SELECT 
        p.Payment_ID, 
        c.Customer_Name, 
        w.Model_Name, 
        p.Amount, 
        p.Payment_Date 
    FROM 
        payment_table p
    JOIN 
        order_table o ON p.Order_ID = o.Order_ID
    JOIN 
        customer_table c ON o.Customer_ID = c.Customer_ID
    JOIN 
        order_detail_table od ON o.Order_ID = od.Order_ID
    JOIN 
        product_table w ON od.Watch_ID = w.Watch_ID
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Payments</title>
    <link rel="stylesheet" href="../../CSS/admin_dashboard.css">
    <style>
        .filter-container {
            margin-bottom: 20px;
        }
        .filter-container input, .filter-container select {
            margin-right: 10px;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterInputs = document.querySelectorAll('.filter-input');
            filterInputs.forEach(input => {
                input.addEventListener('input', filterPayments);
            });
        });

        function filterPayments() {
            const paymentId = document.getElementById('filterPaymentId').value.toLowerCase();
            const customerName = document.getElementById('filterCustomerName').value.toLowerCase();
            const paymentDate = document.getElementById('filterPaymentDate').value;
            const minPrice = parseFloat(document.getElementById('filterMinPrice').value) || 0;
            const maxPrice = parseFloat(document.getElementById('filterMaxPrice').value) || Infinity;

            const rows = document.querySelectorAll('#paymentTableBody tr');
            rows.forEach(row => {
                const id = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const date = row.querySelector('td:nth-child(5)').textContent;
                const amount = parseFloat(row.querySelector('td:nth-child(4)').textContent);

                if (id.includes(paymentId) && name.includes(customerName) && date.includes(paymentDate) && amount >= minPrice && amount <= maxPrice) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</head>
<body>
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

    <main>
        <section class="manage-payments">
            <h2>View Payments</h2>
            <div class="filter-container">
                <input type="text" id="filterPaymentId" class="filter-input" placeholder="Filter by Payment ID">
                <input type="text" id="filterCustomerName" class="filter-input" placeholder="Filter by Customer Name">
                <input type="date" id="filterPaymentDate" class="filter-input" placeholder="Filter by Payment Date">
                <input type="number" id="filterMinPrice" class="filter-input" placeholder="Min Price">
                <input type="number" id="filterMaxPrice" class="filter-input" placeholder="Max Price">
            </div>
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
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['Payment_ID']; ?></td>
                        <td><?php echo $row['Customer_Name']; ?></td>
                        <td><?php echo $row['Model_Name']; ?></td>
                        <td><?php echo $row['Amount']; ?></td>
                        <td><?php echo $row['Payment_Date']; ?></td>
                        <td>
                            <button class="action-btn view-btn" onclick="viewPayment(<?php echo $row['Payment_ID']; ?>)">View</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Admin Dashboard. All rights reserved.</p>
    </footer>

    <!-- Modal for Payment Information -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeInfoModal()">&times;</span>
            <div id="modalContent"></div>
        </div>
    </div>

    <script>
        function viewPayment(paymentId) {
            // Fetch payment details via AJAX
            fetch(`../../PHP/ADMIN_VIEW_PAYMENT/get_payment_details.php?payment_id=${paymentId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('modalContent').innerHTML = data;
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
