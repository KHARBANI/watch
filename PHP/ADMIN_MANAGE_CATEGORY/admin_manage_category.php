<?php
require_once '../db_connection.php';

$error_message = "";
$success_message = "";

// Handle form submission for adding a category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['categoryName'])) {
    $categoryName = $conn->real_escape_string($_POST['categoryName']);
    $categoryStatus = 'Active';

    // Check for duplicate category name
    $checkDuplicate = "SELECT * FROM category_table WHERE Category_Name = '$categoryName'";
    $duplicateResult = $conn->query($checkDuplicate);

    if ($duplicateResult->num_rows == 0) {
        // Insert new category
        $insertCategory = "INSERT INTO category_table (Category_Name, Category_Status) VALUES ('$categoryName', '$categoryStatus')";
        if ($conn->query($insertCategory) === TRUE) {
            $success_message = "New category added successfully";
        } else {
            $error_message = "Error: " . $insertCategory . "<br>" . $conn->error;
        }
    } else {
        $error_message = "Category already exists.";
    }
}

// Fetch categories with optional filtering
$filterName = isset($_GET['filterName']) ? $conn->real_escape_string($_GET['filterName']) : '';
$filterStatus = isset($_GET['filterStatus']) ? $conn->real_escape_string($_GET['filterStatus']) : '';

$sql = "SELECT Category_ID, Category_Name, Category_Status FROM category_table WHERE 1=1";
if ($filterName) {
    $sql .= " AND Category_Name LIKE '%$filterName%'";
}
if ($filterStatus) {
    $sql .= " AND Category_Status = '$filterStatus'";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
    <link rel="stylesheet" href="../../CSS/admin_dashboard.css">
    <style>
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

        .inline-form {
            display: none;
        }

        .error-message {
            color: red;
        }

        .modal-content {
            max-width: 600px;
        }

        .action-btn {
            margin-right: 5px; /* Add space between action buttons */
        }

        .filter-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #categoryFilter {
            width: 200px; /* Adjusted size */
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
        <section class="manage-categories">
            <h2>Manage Categories</h2>
            <div class="filter-container">
                <input type="text" id="categoryFilter" placeholder="Filter categories by name" aria-label="Category filter">
                <select id="statusFilter" aria-label="Status filter">
                    <option value="" disabled selected hidden>Status</option>
                    <option value="">All</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <button id="addCategoryBtn">Add Category</button>
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
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["Category_ID"] . "</td>";
                            echo "<td>" . $row["Category_Name"] . "</td>";
                            echo "<td>" . $row["Category_Status"] . "</td>";
                            echo "<td>";
                            echo "<button class='action-btn view-btn' onclick='viewCategory(" . $row["Category_ID"] . ")'>View</button>";
                            echo "<button class='action-btn edit-btn' onclick='openEditModal(" . $row["Category_ID"] . ")'>Edit</button>";
                            $toggleButtonText = $row["Category_Status"] === 'Active' ? 'Deactivate' : 'Activate';
                            echo "<button class='action-btn delete-btn' onclick='toggleCategoryStatus(" . $row["Category_ID"] . ", \"" . $row["Category_Status"] . "\")'>" . $toggleButtonText . "</button>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No categories found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Admin Dashboard. All rights reserved.</p>
    </footer>

    <!-- Add Category Modal -->
    <div id="addCategoryModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add Category</h2>
            <form id="addCategoryForm" action="admin_manage_category.php" method="POST">
                <label for="categoryName">Name:</label>
                <input type="text" id="categoryName" name="categoryName">
                <input type="hidden" id="categoryStatus" name="categoryStatus" value="Active">
                <button type="submit">Add Category</button>
            </form>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div id="editCategoryModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Edit Category</h2>
            <form id="editCategoryForm">
                <label for="editCategoryName">Name:</label>
                <input type="text" id="editCategoryName" name="editCategoryName">
                <label for="editCategoryStatus">Status:</label>
                <select id="editCategoryStatus" name="editCategoryStatus">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>

    <!-- Modal for Category Information -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeInfoModal()">&times;</span>
            <div id="modalContent"></div>
        </div>
    </div>

    <!-- Popup Message -->
    <div id="popupMessage"></div>

    <?php if (!empty($error_message) || !empty($success_message)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const popupMessage = document.getElementById('popupMessage');
                popupMessage.innerHTML = '<?php echo !empty($error_message) ? $error_message : $success_message; ?>';
                popupMessage.className = '<?php echo !empty($error_message) ? "error" : "success"; ?>';
                popupMessage.style.display = 'block';
                setTimeout(() => {
                    popupMessage.style.display = 'none';
                }, 3000);
            });
        </script>
    <?php endif; ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addCategoryForm = document.getElementById('addCategoryForm');
            const editCategoryForm = document.getElementById('editCategoryForm');
            const addCategoryBtn = document.getElementById('addCategoryBtn');
            const addCategoryModal = document.getElementById('addCategoryModal');
            const editCategoryModal = document.getElementById('editCategoryModal');
            const closeModalBtns = document.querySelectorAll('.close');

            if (addCategoryForm) {
                addCategoryForm.addEventListener('submit', function(event) {
                    const categoryName = document.getElementById('categoryName').value.trim();
                    const namePattern = /^[A-Za-z\s]+$/; // Updated pattern to accept spaces
                    if (categoryName === '') {
                        showPopupMessage('Category name is required.', 'error');
                        event.preventDefault();
                    } else if (!namePattern.test(categoryName)) {
                        showPopupMessage('Category name must contain only letters and spaces.', 'error');
                        event.preventDefault();
                    }
                });
            }

            if (editCategoryForm) {
                editCategoryForm.addEventListener('submit', function(event) {
                    const editCategoryName = document.getElementById('editCategoryName').value.trim();
                    const namePattern = /^[A-Za-z\s]+$/; // Updated pattern to accept spaces
                    if (editCategoryName === '') {
                        showPopupMessage('Category name is required.', 'error');
                        event.preventDefault();
                    } else if (!namePattern.test(editCategoryName)) {
                        showPopupMessage('Category name must contain only letters and spaces.', 'error');
                        event.preventDefault();
                    }
                });
            }

            if (addCategoryBtn) {
                addCategoryBtn.addEventListener('click', () => {
                    addCategoryModal.style.display = 'block';
                });
            }

            closeModalBtns.forEach(btn => {
                btn.addEventListener('click', closeModal);
            });

            window.addEventListener('click', (event) => {
                if (event.target === addCategoryModal || event.target === editCategoryModal) {
                    closeModal();
                }
            });

            function closeModal() {
                addCategoryModal.style.display = 'none';
                editCategoryModal.style.display = 'none';
                if (addCategoryForm) addCategoryForm.reset();
                if (editCategoryForm) editCategoryForm.reset();
            }

            function showPopupMessage(message, type, callback) {
                const popupMessage = document.getElementById('popupMessage');
                popupMessage.innerHTML = message;
                popupMessage.className = type;
                popupMessage.style.display = 'block';
                setTimeout(() => {
                    popupMessage.style.display = 'none';
                    if (callback) callback();
                }, 3000);
            }

            const categoryFilter = document.getElementById('categoryFilter');
            const statusFilter = document.getElementById('statusFilter');
            const tableBody = document.getElementById('categoryTableBody');

            function filterCategories() {
                const filterText = categoryFilter.value.toLowerCase();
                const filterStatus = statusFilter.value;

                Array.from(tableBody.rows).forEach(row => {
                    const categoryName = row.cells[1].innerText.toLowerCase();
                    const categoryStatus = row.cells[2].innerText;

                    const matchesName = categoryName.includes(filterText);
                    const matchesStatus = filterStatus === '' || categoryStatus === filterStatus;

                    row.style.display = matchesName && matchesStatus ? '' : 'none';
                });
            }

            categoryFilter.addEventListener('input', filterCategories);
            statusFilter.addEventListener('change', filterCategories);

            // Set focus back to the category filter input after page reload
            categoryFilter.focus();
        });

        function viewCategory(categoryId) {
            fetch(`view_category.php?id=${categoryId}`)
                .then(response => response.text())
                .then(data => {
                    const modalContent = document.getElementById('modalContent');
                    modalContent.innerHTML = data;
                    const modal = document.getElementById('modal');
                    modal.style.display = 'block';
                })
                .catch(error => console.error('Error:', error));
        }

        function openEditModal(categoryId) {
            fetch(`get_category.php?id=${categoryId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editCategoryName').value = data.Category_Name;
                    document.getElementById('editCategoryStatus').value = data.Category_Status;
                    document.getElementById('editCategoryForm').action = `update_category.php?id=${categoryId}`;
                    const editCategoryModal = document.getElementById('editCategoryModal');
                    editCategoryModal.style.display = 'block';
                })
                .catch(error => console.error('Error:', error));
        }

        document.getElementById('editCategoryForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            const categoryId = new URLSearchParams(this.action.split('?')[1]).get('id');

            fetch(`update_category.php?id=${categoryId}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data.includes('success')) {
                    window.location.reload();
                } else {
                    showPopupMessage(data, 'error');
                }
            })
            .catch(error => console.error('Error:', error));
        });

        function toggleCategoryStatus(categoryId, currentStatus) {
            const newStatus = currentStatus === 'Active' ? 'Inactive' : 'Active';

            fetch(`toggle_category_status.php?id=${categoryId}&status=${newStatus}`, {
                method: 'POST'
            })
            .then(response => response.text())
            .then(data => {
                if (data.includes('success')) {
                    window.location.reload();
                } else {
                    showPopupMessage(data, 'error');
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function closeInfoModal() {
            const modal = document.getElementById('modal');
            modal.style.display = 'none';
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
