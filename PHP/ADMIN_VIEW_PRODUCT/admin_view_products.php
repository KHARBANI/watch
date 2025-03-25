<?php
// Include database connection
require_once '../../PHP/db_connection.php';

// Fetch products from the database
$query = "
    SELECT 
        p.Watch_ID AS id, 
        p.Model_Name AS name, 
        c.Category_Name AS category, 
        p.Price AS price, 
        s.Quantity_Available AS stock,
        i.Image_URL AS image,
        p.Product_Description AS description,
        cm.Case_Name AS case_material,
        sm.Strap_Name AS strap_material,
        m.Movement_Name AS movement_type
    FROM 
        product_table p
    JOIN 
        category_table c ON p.Category_ID = c.Category_ID
    JOIN 
        stock_table s ON p.Watch_ID = s.Watch_ID
    JOIN 
        image_table i ON p.Image_ID = i.Image_ID
    JOIN 
        case_material_table cm ON p.Case_ID = cm.Case_ID
    JOIN 
        strap_material_table sm ON p.Strap_ID = sm.Strap_ID
    JOIN 
        movement_table m ON p.Movement_ID = m.Movement_ID
";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Products</title>
    <link rel="stylesheet" href="../../CSS/admin_dashboard.css">
    <script src="../../JAVA SCRIPT/admin_view_product.js"></script>
    <style>
        .description-cell {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
        .modal-content img {
            display: block;
            margin: 0 auto; /* Center image horizontally */
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
        .filter-container input[type="range"] {
            width: 200px;
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
        <section class="manage-products">
            <h2>View Products</h2>
            <div class="filter-container">
                <input type="text" id="filterId" class="filter-input" placeholder="Filter by ID">
                <input type="text" id="filterName" class="filter-input" placeholder="Filter by Name">
                <input type="number" id="minPrice" class="filter-input" min="0" step="100" placeholder="Min Price" oninput="filterProducts()">
                <input type="number" id="maxPrice" class="filter-input" min="0" step="100" placeholder="Max Price" oninput="filterProducts()">
                <input type="number" id="minStock" class="filter-input" min="0" step="1" placeholder="Min Stock" oninput="filterProducts()">
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="productTableBody">
                    <?php
                    // Loop through the fetched products and display them in the table
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['price']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['stock']) . "</td>";
                        $imageFilename = basename($row['image']);
                        echo "<td><img src='/uploads/" . htmlspecialchars($imageFilename) . "' alt='Product Image' width='50'></td>";
                        echo "<td>";
                        echo '<button class="action-btn view-btn" onclick="viewProduct(' . $row['id'] . ')" aria-label="View product">View</button>';
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

    <!-- Modal for Product Information -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeInfoModal()">&times;</span>
            <div id="modalContent"></div>
        </div>
    </div>

    <script>
        document.getElementById('filterId').addEventListener('input', filterProducts);
        document.getElementById('filterName').addEventListener('input', filterProducts);
        document.getElementById('minPrice').addEventListener('input', filterProducts);
        document.getElementById('maxPrice').addEventListener('input', filterProducts);
        document.getElementById('minStock').addEventListener('input', filterProducts);

        function filterProducts() {
            const filterId = document.getElementById('filterId').value.toLowerCase();
            const filterName = document.getElementById('filterName').value.toLowerCase();
            const minPrice = parseFloat(document.getElementById('minPrice').value);
            const maxPrice = parseFloat(document.getElementById('maxPrice').value);
            const minStock = parseFloat(document.getElementById('minStock').value);
            const rows = document.querySelectorAll('#productTableBody tr');

            rows.forEach(row => {
                const id = row.cells[0].textContent.toLowerCase();
                const name = row.cells[1].textContent.toLowerCase();
                const price = parseFloat(row.cells[3].textContent);
                const stock = parseFloat(row.cells[4].textContent);

                const matchesId = id.includes(filterId);
                const matchesName = name.includes(filterName);
                const matchesMinPrice = isNaN(minPrice) || price >= minPrice;
                const matchesMaxPrice = isNaN(maxPrice) || price <= maxPrice;
                const matchesMinStock = isNaN(minStock) || stock >= minStock;

                if (matchesId && matchesName && matchesMinPrice && matchesMaxPrice && matchesMinStock) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function viewProduct(productId) {
        // Fetch product details using AJAX
        fetch(`get_product_details.php?id=${productId}`)
            .then(response => response.json())
            .then(data => {
                // Populate modal with product details
                const modalContent = document.getElementById('modalContent');
                modalContent.innerHTML = `
                    <h2>${data.name}</h2>
                    <img src="/uploads/${data.image}" alt="Product Image" width="300">
                    <p><strong>Category:</strong> ${data.category}</p>
                    <p><strong>Price:</strong> $${data.price}</p>
                    <p><strong>Stock:</strong> ${data.stock}</p>
                    <p><strong>Case Material:</strong> ${data.case_material}</p>
                    <p><strong>Strap Material:</strong> ${data.strap_material}</p>
                    <p><strong>Movement Type:</strong> ${data.movement_type}</p>
                    <p><strong>Description:</strong> ${data.description}</p>
                `;
                // Display the modal
                const modal = document.getElementById('modal');
                modal.style.display = 'block';
                modal.removeAttribute('inert');
            })
            .catch(error => console.error('Error fetching product details:', error));
    }

        function closeInfoModal() {
            document.getElementById('modal').style.display = 'none';
        }
    </script>
</body>
</html>
