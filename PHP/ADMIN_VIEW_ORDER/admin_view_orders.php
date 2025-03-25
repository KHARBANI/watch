<?php
// Database connection
require_once '../db_connection.php';

// Fetch orders
$query = "SELECT o.Order_ID, c.Customer_Name, p.Model_Name, od.Quantity, od.Unit_Price, (od.Quantity * od.Unit_Price) AS Subtotal, o.Total_Price, o.Order_Status, o.Order_Date, o.Order_Time, s.Street_Address, ci.City_Name, st.State_Name, s.Postal_Code
          FROM order_table o
          JOIN Customer_table c ON o.Customer_ID = c.Customer_ID
          JOIN Order_Detail_table od ON o.Order_ID = od.Order_ID
          JOIN Product_table p ON od.Watch_ID = p.Watch_ID
          JOIN Shipping_Address_table s ON o.Shipping_Address_ID = s.Shipping_Address_ID
          JOIN City_table ci ON s.City_ID = ci.City_ID
          JOIN State_table st ON ci.State_ID = st.State_ID";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders</title>
    <link rel="stylesheet" href="../../CSS/admin_dashboard.css">
    <style>
        .filter-container {
            margin-bottom: 20px;
        }
        .filter-container input, .filter-container select {
            margin-right: 10px;
        }
    </style>
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
        <section class="manage-orders">
            <h2>View Orders</h2>
            <div class="filter-container">
                <input type="text" id="filterOrderId" placeholder="Order ID" oninput="applyFilters()">
                <input type="number" id="filterMinPrice" placeholder="Min Price" oninput="applyFilters()">
                <input type="number" id="filterPrice" placeholder="Max Price" oninput="applyFilters()">
                <input type="date" id="filterDate" onchange="applyFilters()">
                <select id="filterStatus" onchange="applyFilters()">
                    <option value="">All Statuses</option>
                    <option value="Pending">Pending</option>
                    <option value="Shipped">Shipped</option>
                    <option value="Delivered">Delivered</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="orderTableBody">
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['Order_ID']; ?></td>
                        <td><?php echo $row['Customer_Name']; ?></td>
                        <td><?php echo $row['Model_Name']; ?></td>
                        <td><?php echo $row['Quantity']; ?></td>
                        <td><?php echo $row['Total_Price']; ?></td>
                        <td><?php echo $row['Order_Date']; ?></td>
                        <td><?php echo $row['Order_Status']; ?></td>
                        <td>
                            <button class="action-btn view-btn" onclick="viewOrder('<?php echo $row['Order_ID']; ?>')">View</button>
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

    <!-- Modal for Order Information -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeInfoModal()">&times;</span>
            <div id="modalContent"></div>
        </div>
    </div>
    <script src="../../JAVA SCRIPT/admin_view_orders.js"></script>
    <script>
        function viewOrder(orderId) {
            if (!orderId) {
                console.error('Order ID is missing');
                return;
            }
            // Fetch order details using AJAX
            fetch(`get_order_details.php?order_id=${orderId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Populate modal with order details
                    const modalContent = document.getElementById('modalContent');
                    modalContent.innerHTML = `
                        <h2>Order Details</h2>
                        <p><strong>Order ID:</strong> ${data.Order_ID}</p>
                        <p><strong>Customer Name:</strong> ${data.Customer_Name}</p>
                        <p><strong>Product:</strong> ${data.Model_Name}</p>
                        <p><strong>Quantity:</strong> ${data.Quantity}</p>
                        <p><strong>Unit Price:</strong> ${data.Unit_Price}</p>
                        <p><strong>Total Price:</strong> ${data.Total_Price}</p>
                        <p><strong>Status:</strong> ${data.Order_Status}</p>
                        <p><strong>Order Date:</strong> ${data.Order_Date}</p>
                        <p><strong>Order Time:</strong> ${data.Order_Time}</p>
                        <p><strong>Shipping Address:</strong> ${data.Street_Address}, ${data.City_Name}, ${data.State_Name} - ${data.Postal_Code}</p>
                    `;
                    // Show modal
                    document.getElementById('modal').style.display = 'block';
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
        }

        function closeInfoModal() {
            document.getElementById('modal').style.display = 'none';
        }

        function applyFilters() {
            const orderId = document.getElementById('filterOrderId').value.toLowerCase();
            const minPrice = parseFloat(document.getElementById('filterMinPrice').value);
            const maxPrice = parseFloat(document.getElementById('filterPrice').value);
            const date = document.getElementById('filterDate').value;
            const status = document.getElementById('filterStatus').value.toLowerCase();

            const rows = document.querySelectorAll('#orderTableBody tr');
            rows.forEach(row => {
                const rowOrderId = row.cells[0].textContent.toLowerCase();
                const rowPrice = parseFloat(row.cells[4].textContent);
                const rowDate = row.cells[5].textContent;
                const rowStatus = row.cells[6].textContent.toLowerCase();

                let showRow = true;

                if (orderId && !rowOrderId.includes(orderId)) {
                    showRow = false;
                }
                if (!isNaN(minPrice) && rowPrice < minPrice) {
                    showRow = false;
                }
                if (!isNaN(maxPrice) && rowPrice > maxPrice) {
                    showRow = false;
                }
                if (date && rowDate !== date) {
                    showRow = false;
                }
                if (status && rowStatus !== status) {
                    showRow = false;
                }

                row.style.display = showRow ? '' : 'none';
            });
        }
    </script>
</body>
</html>
