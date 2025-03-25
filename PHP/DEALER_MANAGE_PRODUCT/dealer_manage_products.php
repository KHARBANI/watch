<?php
// Start session to manage dealer login (if not already started)
session_start();

// Include database connection
require_once '../../PHP/db_connection.php'; // Ensure this file contains the database connection logic

// Ensure dealer is logged in
if (!isset($_SESSION['dealer_id'])) {
    echo "<script>alert('Please log in as a dealer to manage products.');</script>";
    echo "<script>window.location.href = '../../PHP/INDEX/login.php';</script>";
    exit();
}

$dealer_id = $_SESSION['dealer_id'];

// Fetch products from the database for the logged-in dealer
$query = "
    SELECT 
        p.Watch_ID AS id, 
        p.Model_Name AS name, 
        c.Category_Name AS category, 
        p.Price AS price, 
        s.Quantity_Available AS stock,
        i.Image_URL AS image,
        p.Product_Status AS status,
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
    WHERE 
        p.Dealer_ID = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dealer Manage Products</title>
    <link rel="stylesheet" href="../../CSS/admin_dashboard.css">
    <link rel="stylesheet" href="../../CSS/dealer_dashboard.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        td img {
            display: block;
            margin: 0 auto;
        }
        .action-btn {
            margin: 2px;
        }
        td:last-child {
            width: 250px; /* Further increase the width of the actions column */
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
            <h1 id="manage-products-heading">Manage Products</h1>
            <div class="filter-container">
                <input type="text" id="productFilter" placeholder="Filter products by name" aria-label="Product filter">
                <input type="number" id="minPriceFilter" placeholder="Min Price" aria-label="Minimum price filter">
                <input type="number" id="maxPriceFilter" placeholder="Max Price" aria-label="Maximum price filter">
                <select id="stockFilter" aria-label="Stock filter">
                    <option value="" disabled selected hidden>Stock</option>
                    <option value="">All</option>
                    <option value="0">Out of Stock</option>
                    <option value="1">In Stock</option>
                </select>
            </div>
            <button id="addProductBtn" aria-label="Add Product" onclick="openAddProductModal()">Add Product</button>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Image</th>
                        <th>Status</th>
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
                        echo "<center><td><img src='/uploads/" . htmlspecialchars($imageFilename) . "' alt='Product Image' width='50'></td></center>";
                        echo "<td>" . htmlspecialchars(ucfirst($row['status'])) . "</td>";
                        echo "<td>";
                        echo '<button class="action-btn view-btn" onclick="viewProduct(' . $row['id'] . ')" aria-label="View product">View</button>';
                        echo '<button class="action-btn edit-btn" onclick="editProduct(' . $row['id'] . ')" aria-label="Edit product">Edit</button>';
                        if (strtolower($row['status']) == 'active') {
                            echo '<button class="action-btn delete-btn" onclick="toggleProductStatus(' . $row['id'] . ', \'Inactive\')" aria-label="Deactivate product">Deactivate</button>';
                        } else {
                            echo '<button class="action-btn activate-btn" onclick="toggleProductStatus(' . $row['id'] . ', \'Active\')" aria-label="Activate product">Activate</button>';
                        }
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
    <!-- Modal for Add Product -->
    <div id="addProductModal" class="modal" role="dialog" aria-labelledby="add-product-title" inert>
        <div class="modal-content">
            <span class="close" onclick="closeAddProductModal()" aria-label="Close modal">&times;</span>
            <h2 id="add-product-title">Add Product</h2>
            <form id="addProductForm" method="POST" action="add_product.php" enctype="multipart/form-data">
                <label for="productName">Product Name:</label>
                <input type="text" id="productName" name="productName" required>
                <label for="image">Image:</label>
                <input type="file" id="image" name="image" required>
                <label for="productPrice">Price:</label>
                <input type="number" id="productPrice" name="productPrice" required>
                <label for="caseMaterial">Case Material:</label>
                <select name="caseMaterial" id="caseMaterial">
                    <option value="" disabled selected hidden>Select Case Material</option>
                    <?php
                    // Fetch case materials
                    $caseQuery = "SELECT Case_ID, Case_Name FROM case_material_table";
                    $caseResult = mysqli_query($conn, $caseQuery);
                    while ($caseRow = mysqli_fetch_assoc($caseResult)) {
                        echo "<option value='{$caseRow['Case_ID']}'>{$caseRow['Case_Name']}</option>";
                    }
                    ?>
                    <option value="new">Add New Case Material</option>
                </select>
                <div id="newCaseMaterialInput" style="display:none;">
                    <input type="text" id="newCaseMaterialText" placeholder="Enter new case material">
                    <button type="button" onclick="addNewCaseMaterial()">Add</button>
                </div>
                <label for="strapMaterial">Strap Material:</label>
                <select name="strapMaterial" id="strapMaterial">
                    <option value="" disabled selected hidden>Select Strap Material</option>
                    <?php
                    // Fetch strap materials
                    $strapQuery = "SELECT Strap_ID, Strap_Name FROM strap_material_table";
                    $strapResult = mysqli_query($conn, $strapQuery);
                    while ($strapRow = mysqli_fetch_assoc($strapResult)) {
                        echo "<option value='{$strapRow['Strap_ID']}'>{$strapRow['Strap_Name']}</option>";
                    }
                    ?>
                    <option value="new">Add New Strap Material</option>
                </select>
                <div id="newStrapMaterialInput" style="display:none;">
                    <input type="text" id="newStrapMaterialText" placeholder="Enter new strap material">
                    <button type="button" onclick="addNewStrapMaterial()">Add</button>
                </div>
                <label for="movementType">Movement Type:</label>
                <select name="movementType" id="movementType">
                    <option value="" disabled selected hidden>Select Movement Type</option>
                    <?php
                    // Fetch movement types
                    $movementQuery = "SELECT Movement_ID, Movement_Name FROM movement_table";
                    $movementResult = mysqli_query($conn, $movementQuery);
                    while ($movementRow = mysqli_fetch_assoc($movementResult)) {
                        echo "<option value='{$movementRow['Movement_ID']}'>{$movementRow['Movement_Name']}</option>";
                    }
                    ?>
                    <option value="new">Add New Movement Type</option>
                </select>
                <div id="newMovementTypeInput" style="display:none;">
                    <input type="text" id="newMovementTypeText" placeholder="Enter new movement type">
                    <button type="button" onclick="addNewMovementType()">Add</button>
                </div>
                <label for="productDescription">Product Description:</label>
                <textarea id="productDescription" name="productDescription" required></textarea>
                <input type="hidden" name="productStatus" value="active">
                <button type="submit" aria-label="Submit new product">Add Product</button>
            </form>
        </div>
    </div>

    <!-- Modal for Edit Product -->
    <div id="editProductModal" class="modal" role="dialog" aria-labelledby="edit-product-title" inert>
        <div class="modal-content">
            <span class="close" onclick="closeEditProductModal()" aria-label="Close modal">&times;</span>
            <h2 id="edit-product-title">Edit Product</h2>
            <form id="editProductForm" method="POST" action="edit_product.php" enctype="multipart/form-data">
                <input type="hidden" id="editProductId" name="productId">
                <label for="editProductName">Product Name:</label>
                <input type="text" id="editProductName" name="productName" required>
                <label for="editImage">Image:</label>
                <input type="file" id="editImage" name="image">
                <label for="editProductPrice">Price:</label>
                <input type="number" id="editProductPrice" name="productPrice" required>
                <label for="editCaseMaterial">Case Material:</label>
                <select name="caseMaterial" id="editCaseMaterial" onchange="toggleNewCaseMaterialInput(this)">
                    <option value="" disabled selected hidden>Select Case Material</option>
                    <!-- Options will be populated dynamically -->
                </select>
                <div id="newCaseMaterialInput" style="display:none;"></div>
                <label for="editStrapMaterial">Strap Material:</label>
                <select name="strapMaterial" id="editStrapMaterial" onchange="toggleNewStrapMaterialInput(this)">
                    <option value="" disabled selected hidden>Select Strap Material</option>
                    <!-- Options will be populated dynamically -->
                </select>
                <div id="newStrapMaterialInput" style="display:none;"></div>
                <label for="editMovementType">Movement Type:</label>
                <select name="movementType" id="editMovementType" onchange="toggleNewMovementTypeInput(this)">
                    <option value="" disabled selected hidden>Select Movement Type</option>
                    <!-- Options will be populated dynamically -->
                </select>
                <div id="newMovementTypeInput" style="display:none;"></div>
                <label for="editProductDescription">Product Description:</label>
                <textarea id="editProductDescription" name="productDescription" required></textarea>
                <button type="submit" aria-label="Submit edited product">Save Changes</button>
            </form>
        </div>
    </div>

    <!-- Modal for Product information -->
    <div id="modal" class="modal" role="dialog" aria-labelledby="modal-title" inert>
        <div class="modal-content">
            <span class="close" onclick="closeInfoModal()" aria-label="Close modal">&times;</span>
            <div id="modalContent"></div>
        </div>
    </div>

    <div id="popupMessage"></div>
    <script src="../../JAVA SCRIPT/dealer_manage_products.js"></script>
    <script>
    function viewProduct(productId) {
        // Fetch product details using AJAX
        fetch(`view_product.php?id=${productId}`)
            .then(response => response.json())
            .then(data => {
                // Populate modal with product details
                const modalContent = document.getElementById('modalContent');
                modalContent.innerHTML = `
                    <h2>${data.name}</h2>
                    <center><img src="/uploads/${data.image}" alt="Product Image" width="300"></center>
                    <p><strong>Category:</strong> ${data.category}</p>
                    <p><strong>Price:</strong> $${data.price}</p>
                    <p><strong>Stock:</strong> ${data.stock}</p>
                    <p><strong>Case Material:</strong> ${data.case_material}</p>
                    <p><strong>Strap Material:</strong> ${data.strap_material}</p>
                    <p><strong>Movement Type:</strong> ${data.movement_type}</p>
                    <p><strong>Description:</strong> ${data.description}</p>
                    <p><strong>Status:</strong> ${data.status}</p>
                `;
                // Display the modal
                const modal = document.getElementById('modal');
                modal.style.display = 'block';
                modal.removeAttribute('inert');
            })
            .catch(error => console.error('Error fetching product details:', error));
    }

    function closeInfoModal() {
    const modal = document.getElementById('modal');
    modal.style.display = 'none';
    modal.setAttribute('inert', '');
    }

    function toggleProductStatus(productId, newStatus) {
        // Update product status using AJAX
        fetch(`toggle_product_status.php?id=${productId}&status=${newStatus}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Reload the page to reflect the changes
                } else {
                    alert('Failed to update product status');
                }
            })
            .catch(error => console.error('Error updating product status:', error));
    }
    </script>
</body>
</html>