<?php
// Start session to manage dealer login (if not already started)
session_start();

// Include database connection
require_once '../../PHP/db_connection.php'; // Ensure this file contains the database connection logic

// Ensure dealer is logged in
if (!isset($_SESSION['dealer_id'])) {
    echo "<script>window.location.href = '../../PHP/INDEX/login.php';</script>";
    exit();
}

$dealerId = $_SESSION['dealer_id'];

// Fetch products from the database
$query = "
    SELECT 
        s.Stock_ID AS id,
        s.Watch_ID AS watch_id,
        s.Quantity_Available AS stock, 
        s.Minimum_Stock_Level AS min_stock, 
        s.Last_Updated AS last_updated
    FROM 
        stock_table s
    JOIN 
        product_table p ON s.Watch_ID = p.Watch_ID
    WHERE 
        p.Dealer_ID = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $dealerId);
$stmt->execute();
$result = $stmt->get_result();

// Get watch_id from query parameter if available
$watchId = isset($_GET['watch_id']) ? intval($_GET['watch_id']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dealer Manage Stocks</title>
    <link rel="stylesheet" href="../../CSS/admin_dashboard.css">
    <link rel="stylesheet" href="../../CSS/dealer_dashboard.css">
    <style>
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
    <main>
        <section class="manage-products" role="region" aria-labelledby="manage-products-heading">
            <h1 id="manage-products-heading">Manage Stock</h1>
            <button id="addStockBtn" aria-label="Add Stock" onclick="openAddStockModal()">Add Stock</button>
            <table>
                <thead>
                    <tr>
                        <th>Stock ID</th>
                        <th>Watch ID</th>
                        <th>Quantity Available</th>
                        <th>Minimum Stock Level</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="productTableBody">
                    <?php
                    // Loop through the fetched products and display them in the table
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['watch_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['stock']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['min_stock']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['last_updated']) . "</td>";
                        echo "<td>";
                        echo '<button class="action-btn view-btn" onclick="viewStock(' . $row['id'] . ')" aria-label="View stock">View</button>';
                        echo '<button class="action-btn edit-btn" onclick="editStock(' . $row['id'] . ')" aria-label="Edit stock">Edit</button>';
                        echo '<button class="action-btn delete-btn" onclick="toggleProductStatus(' . $row['id'] . ')" aria-label="Toggle product status">Deactivate</button>';
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Admin Dashboard. All rights reserved.</p>
    </footer>
    <!-- Modal for Add Stock -->
    <div id="addStockModal" class="modal" role="dialog" aria-labelledby="add-stock-title" aria-hidden="true">
        <div class="modal-content">
            <span class="close" onclick="closeAddStockModal()" aria-label="Close modal">&times;</span>
            <h2 id="add-stock-title">Add Stock</h2>
            <form id="addStockForm" method="POST" action="../../PHP/DEALER_MANAGE_PRODUCT/add_stock.php">
                <label for="stockWatchId">Watch ID:</label>
                <input type="number" id="stockWatchId" name="watch_id" value="<?php echo htmlspecialchars($watchId); ?>" required>
                <label for="stockQuantity">Stock Quantity:</label>
                <input type="number" id="stockQuantity" name="stockQuantity" required>
                <label for="minStockLevel">Minimum Stock Level:</label>
                <input type="number" id="minStockLevel" name="minStockLevel" required>
                <button type="submit" aria-label="Submit new stock">Add Stock</button>
            </form>
        </div>
    </div>
    <!-- Modal for Editing Stock -->
    <div id="editStockModal" class="modal" role="dialog" aria-labelledby="edit-stock-title" aria-hidden="true">
        <div class="modal-content">
            <span class="close" onclick="closeEditStockModal()" aria-label="Close modal">&times;</span>
            <h2 id="edit-stock-title">Edit Stock</h2>
            <form id="editStockForm" method="POST" action="../PHP/edit_stock.php">
                <label for="editStockQuantity">Stock Quantity:</label>
                <input type="number" id="editStockQuantity" name="editStockQuantity" required>
                <label for="editMinStockLevel">Minimum Stock Level:</label>
                <input type="number" id="editMinStockLevel" name="editMinStockLevel" required>
                <button type="submit" aria-label="Submit edited stock">Edit Stock</button>
            </form>
        </div>
    </div>
    <!-- Modal for Stock information -->
    <div id="modal" class="modal" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
        <div class="modal-content">
            <span class="close" onclick="closeInfoModal()" aria-label="Close modal">&times;</span>
            <div id="modalContent"></div>
        </div>
    </div>
    <div id="popupMessage" role="alert"></div>
    <script>
        function openAddStockModal() {
            document.getElementById('addStockModal').style.display = 'block';
        }

        function closeAddStockModal() {
            document.getElementById('addStockModal').style.display = 'none';
        }

        function closeEditStockModal() {
            document.getElementById('editStockModal').style.display = 'none';
        }

        function closeInfoModal() {
            document.getElementById('modal').style.display = 'none';
        }

        function showPopupMessage(message, type) {
            const popup = document.getElementById('popupMessage');
            popup.textContent = message;
            popup.className = type;
            popup.style.display = 'block';
            setTimeout(() => {
                popup.style.display = 'none';
            }, 5000); // Increase duration to 5000ms (5 seconds)
        }

        document.getElementById('addStockForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        showPopupMessage('Stock added successfully', 'success');
                        closeAddStockModal();
                        setTimeout(() => location.reload(), 5000); // Delay reload by 5 seconds
                    } else {
                        showPopupMessage('Failed to add stock: ' + data.message, 'error');
                    }
                } catch (error) {
                    console.error('Error parsing JSON:', error, text);
                    showPopupMessage('Failed to add stock: Invalid server response', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showPopupMessage('Failed to add stock: ' + error.message, 'error');
            });
        });

        document.getElementById('editStockForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        showPopupMessage('Stock updated successfully', 'success');
                        closeEditStockModal();
                        setTimeout(() => location.reload(), 1000); // Delay reload by 1 second
                    } else {
                        showPopupMessage('Failed to update stock: ' + data.message, 'error');
                    }
                } catch (error) {
                    console.error('Error parsing JSON:', error, text);
                    showPopupMessage('Failed to update stock: Invalid server response', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showPopupMessage('Failed to update stock: ' + error.message, 'error');
            });
        });

        function viewStock(stockId) {
            fetch(`../../PHP/DEALER_MANAGE_PRODUCT/view_stock.php?stock_id=${stockId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        showPopupMessage('Failed to fetch stock details: ' + data.error, 'error');
                    } else {
                        const modalContent = `
                            <h2>Stock Details</h2>
                            <p><strong>Stock ID:</strong> ${stockId}</p>
                            <p><strong>Watch ID:</strong> ${data.watch_id}</p>
                            <p><strong>Quantity Available:</strong> ${data.stock}</p>
                            <p><strong>Minimum Stock Level:</strong> ${data.min_stock}</p>
                            <p><strong>Last Updated:</strong> ${data.last_updated}</p>
                        `;
                        document.getElementById('modalContent').innerHTML = modalContent;
                        document.getElementById('modal').style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error fetching stock details:', error);
                    showPopupMessage('Failed to fetch stock details: ' + error.message, 'error');
                });
        }

        function editStock(stockId) {
            fetch(`../../PHP/DEALER_MANAGE_PRODUCT/view_stock.php?stock_id=${stockId}`)
                .then(response => response.text())
                .then(text => {
                    try {
                        const data = JSON.parse(text);
                        document.getElementById('editStockQuantity').value = data.stock;
                        document.getElementById('editMinStockLevel').value = data.min_stock;
                        document.getElementById('editStockForm').action = `../../PHP/DEALER_MANAGE_PRODUCT/edit_stock.php?stock_id=${stockId}`;
                        document.getElementById('editStockModal').style.display = 'block';
                    } catch (error) {
                        console.error('Error parsing JSON:', error, text);
                        showPopupMessage('Failed to fetch stock details: Invalid server response', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error fetching stock details:', error);
                    showPopupMessage('Failed to fetch stock details: ' + error.message, 'error');
                });
        }

        function fetchStockData() {
            fetch(`../../PHP/DEALER_MANAGE_PRODUCT/fetch_stock.php`)
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.getElementById('productTableBody');
                    tableBody.innerHTML = '';
                    data.forEach(row => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${row.id}</td>
                            <td>${row.watch_id}</td>
                            <td>${row.stock}</td>
                            <td>${row.min_stock}</td>
                            <td>${row.last_updated}</td>
                            <td>
                                <button class="action-btn view-btn" onclick="viewStock(${row.id})" aria-label="View stock">View</button>
                                <button class="action-btn edit-btn" onclick="editStock(${row.id})" aria-label="Edit stock">Edit</button>
                            </td>
                        `;
                        tableBody.appendChild(tr);
                    });
                })
                .catch(error => {
                    console.error('Error fetching stock data:', error);
                    showPopupMessage('Failed to fetch stock data: ' + error.message, 'error');
                });
        }

        // Initial fetch
        fetchStockData();
    </script>
</body>
</html>