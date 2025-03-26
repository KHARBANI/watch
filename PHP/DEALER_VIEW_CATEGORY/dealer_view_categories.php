<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Categories</title>
    <link rel="stylesheet" href="../../CSS/admin_dashboard.css">
    <link rel="stylesheet" href="../../CSS/dealer_dashboard.css">
    <style>
        .category-filter-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 50px;
        margin-bottom: 20px;
        }

        .category-filter-container input,.category-filter-container select {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        width: 200px;
        }
    </style>
</head>
<body>
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
        <section class="manage-categories">
            <h2>View Categories</h2>
            <div class="category-filter-container">
                <input type="text" id="filterInput" class="filter-input" placeholder="Filter categories...">
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="categoryTableBody">
                    <?php
                    // Database connection
                    $conn = new mysqli('localhost', 'root', '', 'watch_store');
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Fetch categories
                    $sql = "SELECT Category_ID, Category_Name, Category_Status FROM category_table";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["Category_ID"] . "</td>";
                            echo "<td>" . $row["Category_Name"] . "</td>";
                            echo "<td>" . $row["Category_Status"] . "</td>";
                            echo '<td><button class="action-btn view-btn" onclick="viewCategory(' . $row["Category_ID"] . ')">View</button></td>';
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No categories found</td></tr>";
                    }

                    $conn->close();
                    ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Admin Dashboard. All rights reserved.</p>
    </footer>

    <!-- Modal for Category information -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeInfoModal()">&times;</span>
            <div id="modalContent"></div>
        </div>
    </div>

    <script>
        // Filtering functionality
        document.getElementById('filterInput').addEventListener('keyup', function() {
            var filter = this.value.toLowerCase();
            var rows = document.querySelectorAll('#categoryTableBody tr');
            rows.forEach(function(row) {
                var cells = row.getElementsByTagName('td');
                var match = false;
                for (var i = 0; i < cells.length; i++) {
                    if (cells[i].textContent.toLowerCase().indexOf(filter) > -1) {
                        match = true;
                        break;
                    }
                }
                row.style.display = match ? '' : 'none';
            });
        });

        // View category details
        function viewCategory(categoryId) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_category_details.php?id=' + categoryId, true);
            xhr.onload = function() {
                if (this.status == 200) {
                    document.getElementById('modalContent').innerHTML = this.responseText;
                    document.getElementById('modal').style.display = 'block';
                }
            };
            xhr.send();
        }

        // Close modal
        function closeInfoModal() {
            document.getElementById('modal').style.display = 'none';
        }
    </script>
</body>
</html>
