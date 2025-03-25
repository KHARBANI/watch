<?php
require_once '../db_connection.php';

// Fetch all customers from the database
$sql = "SELECT Customer_ID, Customer_Name, Email, Phone, Account_Status FROM customer_table";
$result = $conn->query($sql);

// Handle activation/deactivation request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status'])) {
    $customerId = $_POST['customer_id'];
    $newStatus = $_POST['new_status'];
    $updateSql = "UPDATE customer_table SET Account_Status = ? WHERE Customer_ID = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("si", $newStatus, $customerId);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_manage_customer.php"); // Refresh the page
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers</title>

    <link rel="stylesheet" href="../../CSS/admin_dashboard.css">
    <link rel="stylesheet" href="../../CSS/admin_manage_user.css">
    <script src="../../JAVA SCRIPT/admin_manage_customer.js"></script>
    <style>
        /* Dropdown Styles */
        .dropdown-setting {
            display: inline-block;
            margin: 0;
        }
        .dropdown-content-setting {
            display: none;
            position: absolute;
            background-color: #333;
            min-width: 160px;
            z-index: 20;
        }
        .dropdown-content-setting a {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .dropdown-setting:hover .dropdown-content-setting {
            display: block;
        }
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
        <section class="manage-users">
            <h2>Manage Customers</h2>
            <div class="filter-container">
                <input type="text" id="filterId" placeholder="Filter by ID">
                <input type="text" id="filterName" placeholder="Filter by Name">
                <select id="filterStatus">
                    <option value="">All</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Customer ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Account Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $buttonText = $row['Account_Status'] === 'Active' ? 'Deactivate' : 'Activate';
                                $newStatus = $row['Account_Status'] === 'Active' ? 'Inactive' : 'Active';
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['Customer_ID']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['Customer_Name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['Email']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['Phone']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['Account_Status']) . "</td>";
                                echo '<td>
                                        <button class="action-btn view-btn" onclick="viewCustomer(' . $row['Customer_ID'] . ')">View</button>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="customer_id" value="' . $row['Customer_ID'] . '">
                                            <input type="hidden" name="new_status" value="' . $newStatus . '">
                                            <button class="action-btn delete-btn" name="toggle_status">' . $buttonText . '</button>
                                        </form>
                                      </td>';
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No customers found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Admin Dashboard. All rights reserved.</p>
    </footer>

    <!-- Modal for Customer Information -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div id="modalContent"></div>
        </div>
    </div>

    <script>
        // Function to handle viewing customer details
        function viewCustomer(id) {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "get_customer_details.php?id=" + id, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById("modalContent").innerHTML = xhr.responseText;
                    document.getElementById("modal").style.display = "block";
                }
            };
            xhr.send();
        }

        // Function to close the modal
        function closeModal() {
            document.getElementById("modal").style.display = "none";
        }

        // Function to filter customers dynamically
        function filterCustomers() {
            const filterId = document.getElementById('filterId').value.toLowerCase();
            const filterName = document.getElementById('filterName').value.toLowerCase();
            const filterStatus = document.getElementById('filterStatus').value.toLowerCase();
            const rows = document.querySelectorAll('#userTableBody tr');

            rows.forEach(row => {
                const id = row.cells[0].textContent.toLowerCase();
                const name = row.cells[1].textContent.toLowerCase();
                const status = row.cells[4].textContent.toLowerCase();

                const matchesId = id.includes(filterId);
                const matchesName = name.includes(filterName);
                const matchesStatus = filterStatus === '' || status === filterStatus;

                if (matchesId && matchesName && matchesStatus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        document.getElementById('filterId').addEventListener('input', filterCustomers);
        document.getElementById('filterName').addEventListener('input', filterCustomers);
        document.getElementById('filterStatus').addEventListener('change', filterCustomers);
    </script>
</body>
</html>

<?php
$conn->close();
?>