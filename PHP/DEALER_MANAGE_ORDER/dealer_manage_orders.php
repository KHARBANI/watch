<?php
// Database connection
require_once '../../PHP/db_connection.php';

session_start();

if (isset($_SESSION['dealer_id'])) {
    $dealer_id = intval($_SESSION['dealer_id']);

    // Fetch orders
    $sql = "SELECT o.Order_ID, c.Customer_Name, p.Model_Name, od.Quantity, od.Unit_Price, o.Order_Status 
            FROM order_table o
            JOIN customer_table c ON o.Customer_ID = c.Customer_ID
            JOIN order_detail_table od ON o.Order_ID = od.Order_ID
            JOIN product_table p ON od.Watch_ID = p.Watch_ID
            WHERE p.Dealer_ID = ?";
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
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .order-id-column {
            width: 100px;
        }
        .action-column {
            width: 200px;
        }
        .action-btn {
            margin: 2px 0;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
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
        <section class="manage-orders">
            <h2>Manage Orders</h2>
            <div class="filter-container">
                <select id="statusFilter" onchange="filterOrders()">
                    <option value="" disabled selected hidden>Filter by Status</option>
                    <option value="All">All</option>
                    <option value="Delivered">Delivered</option>
                    <option value="Shipped">Shipped</option>
                    <option value="Processing">Processing</option>
                    <option value="Pending">Pending</option>
                </select>
            </div>
            <table>
                <thead>
                    <tr>
                        <th class="order-id-column">Order ID</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th class="action-column">Action</th>
                    </tr>
                </thead>
                <tbody id="productTableBody">
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['Order_ID']}</td>
                                    <td>{$row['Customer_Name']}</td>
                                    <td>{$row['Model_Name']}</td>
                                    <td>{$row['Quantity']}</td>
                                    <td>{$row['Unit_Price']}</td>
                                    <td>{$row['Order_Status']}</td>
                                    <td class='action-column'>
                                        <button class='action-btn view-btn' onclick='viewOrder({$row['Order_ID']})'>View</button>
                                        <button class='action-btn edit-btn' onclick='showUpdateModal({$row['Order_ID']})'>Update Status</button>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No orders found</td></tr>";
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

    <!-- Modal for Order Information -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeInfoModal()">&times;</span>
            <div id="modalContent"></div>
        </div>
    </div>

    <!-- Modal for Updating Order Status -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeUpdateModal()">&times;</span>
            <h2>Update Order Status</h2>
            <form id="updateForm">
                <input type="hidden" id="updateOrderId">
                <label for="orderStatus">Order Status:</label>
                <select id="orderStatus" name="orderStatus">
                    <option value="Delivered">Delivered</option>
                    <option value="Shipped">Shipped</option>
                    <option value="Processing">Processing</option>
                    <option value="Pending">Pending</option>
                </select>
                <button type="button" onclick="updateOrderStatus()">Update</button>
            </form>
        </div>
    </div>

    <div id="popupMessage"></div>

    <script>
        function viewOrder(orderId) {
            // Fetch order details via AJAX
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "../../PHP/DEALER_MANAGE_ORDER/get_order_details.php?order_id=" + orderId, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById("modalContent").innerHTML = xhr.responseText;
                    document.getElementById("modal").style.display = "block";
                }
            };
            xhr.send();
        }

        function closeInfoModal() {
            document.getElementById("modal").style.display = "none";
        }

        function showUpdateModal(orderId) {
            document.getElementById("updateOrderId").value = orderId;
            document.getElementById("updateModal").style.display = "block";
        }

        function closeUpdateModal() {
            document.getElementById("updateModal").style.display = "none";
        }

        function updateOrderStatus() {
            var orderId = document.getElementById("updateOrderId").value;
            var orderStatus = document.getElementById("orderStatus").value;

            // Update order status via AJAX
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "../../PHP/DEALER_MANAGE_ORDER/update_order_status.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    showPopupMessage("Order status updated successfully!", "success");
                    closeUpdateModal();
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else if (xhr.readyState == 4) {
                    showPopupMessage("Error updating order status.", "error");
                }
            };
            xhr.send("order_id=" + orderId + "&order_status=" + orderStatus);
        }

        function showPopupMessage(message, type) {
            var popup = document.getElementById("popupMessage");
            popup.innerHTML = message;
            popup.className = type;
            popup.style.display = "block";
            setTimeout(function() {
                popup.style.display = "none";
            }, 3000);
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById("modal")) {
                document.getElementById("modal").style.display = "none";
            }
            if (event.target == document.getElementById("updateModal")) {
                document.getElementById("updateModal").style.display = "none";
            }
        }
    </script>
</body>
</html>
