<?php
require_once 'db_connection.php';

// Fetch dealers from the database
$query = "SELECT Dealer_ID, Dealer_Name, Email, Phone, Account_Status FROM dealer_table";
$result = $conn->query($query);
$dealers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dealers[] = $row;
    }
}

// Fetch all states
$sql = "SELECT State_ID, State_Name FROM state_table";
$result = $conn->query($sql);
$states = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $states[] = $row;
    }
}

// Fetch all brands
$sql = "SELECT Brand_ID, Brand_Name FROM brand_table";
$result = $conn->query($sql);
$brands = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $brands[] = $row;
    }
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Dealers</title>
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
            <h2>Manage Dealers</h2>
            <div class="filter-container">
                <input type="text" id="filterId" placeholder="Filter by ID">
                <input type="text" id="filterName" placeholder="Filter by Name">
                <select id="filterStatus">
                    <option value="">All</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <button id="addUserBtn" onclick="openModal()">Add Dealer</button>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Account Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="dealerTableBody">
                        <?php foreach ($dealers as $dealer): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($dealer['Dealer_ID']); ?></td>
                            <td><?php echo htmlspecialchars($dealer['Dealer_Name']); ?></td>
                            <td><?php echo htmlspecialchars($dealer['Email']); ?></td>
                            <td><?php echo htmlspecialchars($dealer['Phone']); ?></td>
                            <td><?php echo htmlspecialchars($dealer['Account_Status']); ?></td>
                            <td>
                                <button type="button" class="action-btn edit-btn" onclick="openEditModal(<?php echo $dealer['Dealer_ID']; ?>)">Edit</button>
                                <button type="button" class="action-btn view-btn" onclick="openViewModal(<?php echo $dealer['Dealer_ID']; ?>)">View</button>
                                <form method="POST" action="toggle_dealer_status.php" style="display:inline;">
                                    <input type="hidden" name="dealer_id" value="<?php echo $dealer['Dealer_ID']; ?>">
                                    <button type="submit" class="action-btn delete-btn">
                                        <?php echo $dealer['Account_Status'] === 'Active' ? 'Deactivate' : 'Activate'; ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Admin Dashboard. All rights reserved.</p>
    </footer>

    <!-- Add Dealer Modal -->
    <div id="addUserModal" class="modal" aria-hidden="true">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Add Dealer</h2>
            <form id="addDealerForm" method="POST">
                <input type="hidden" name="add_dealer" value="1">
                <label for="dealerName">Dealer Name:</label>
                <input type="text" id="dealerName" name="dealer_name" placeholder="Enter dealer's name">
                <span id="dealer_nameError" class="error-message"></span>
                <label for="dealerEmail">Email:</label>
                <input type="email" id="dealerEmail" name="email" placeholder="Enter dealer's email">
                <span id="emailError" class="error-message"></span>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter dealer's password">
                <span id="passwordError" class="error-message"></span>
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" placeholder="Enter dealer's phone number">
                <span id="phoneError" class="error-message"></span>
                <label for="state">State:</label>
                <select id="state" name="state">    
                    <option value="">Select State</option>
                    <?php foreach ($states as $state): ?>
                        <option value="<?php echo htmlspecialchars($state['State_ID']); ?>">
                            <?php echo htmlspecialchars($state['State_Name']); ?>
                        </option>
                    <?php endforeach; ?>
                    <option value="new">+ Add New State</option>
                </select>
                <div id="stateForm" class="inline-form">
                    <input type="text" id="newStateInput" placeholder="Enter new state name">
                    <button type="button" id="addStateBtn">Add</button>
                    <span id="stateError" class="error-message"></span>
                </div>
                <label for="city">City:</label>
                <select id="city" name="city">
                    <option value="">Select City</option>
                    <option value="new">+ Add New City</option>
                </select>
                <span id="cityError" class="error-message"></span>
                <div id="cityForm" class="inline-form">
                    <input type="text" id="newCityInput" placeholder="Enter new city name">
                    <button type="button" id="addCityBtn">Add</button>
                    <span id="cityError" class="error-message"></span>
                </div>
                <label for="brand">Brand:</label>
                <select id="brand" name="brand">
                    <option value="">Select Brand</option>
                    <?php foreach ($brands as $brand): ?>
                        <option value="<?php echo htmlspecialchars($brand['Brand_ID']); ?>">
                            <?php echo htmlspecialchars($brand['Brand_Name']); ?>
                        </option>
                    <?php endforeach; ?>
                    <option value="new">+ Add New Brand</option>
                </select>
                <div id="brandForm" class="inline-form">
                    <input type="text" id="newBrandInput" placeholder="Enter new brand name">
                    <button type="button" id="addBrandBtn">Add</button>
                    <span id="brandError" class="error-message"></span>
                </div>
                <label for="address">Street:</label>
                <input type="text" id="address" name="street" placeholder="Enter dealer's street">
                <span id="streetError" class="error-message"></span>
                <label for="postalCode">Postal Code:</label>
                <input type="text" id="postalCode" name="postal_code" placeholder="Enter postal code">
                <span id="postal_codeError" class="error-message"></span>
                <label for="GST">GST Number:</label>
                <input type="text" id="GST" name="gst_number" placeholder="Enter dealer's GST number">
                <span id="gst_numberError" class="error-message"></span>
                <label for="PAN">PAN Number:</label>
                <input type="text" id="PAN" name="pan_number" placeholder="Enter dealer's PAN number">
                <span id="pan_numberError" class="error-message"></span>
                <input type="hidden" name="account_status" value="Active">
                <button type="button" id="submitDealerForm">Add User</button>
            </form>
        </div>
    </div>

    <!-- Edit Dealer Modal -->
    <div id="editUserModal" class="modal" aria-hidden="true">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Dealer</h2>
            <form id="editDealerForm" method="POST">
                <input type="hidden" name="dealer_id" id="editDealerId">
                <label for="editDealerName">Dealer Name:</label>
                <input type="text" id="editDealerName" name="dealer_name" placeholder="Enter dealer's name">
                <span id="editDealerNameError" class="error-message"></span>
                <label for="editDealerEmail">Email:</label>
                <input type="email" id="editDealerEmail" name="email" placeholder="Enter dealer's email">
                <span id="editDealerEmailError" class="error-message"></span>
                <label for="editPhone">Phone:</label>
                <input type="text" id="editPhone" name="phone" placeholder="Enter dealer's phone number">
                <span id="editPhoneError" class="error-message"></span>
                <label for="editState">State:</label>
                <select id="editState" name="state">    
                    <option value="">Select State</option>
                    <?php foreach ($states as $state): ?>
                        <option value="<?php echo htmlspecialchars($state['State_ID']); ?>">
                            <?php echo htmlspecialchars($state['State_Name']); ?>
                        </option>
                    <?php endforeach; ?>
                    <option value="new">+ Add New State</option>
                </select>
                <div id="editStateForm" class="inline-form">
                    <input type="text" id="editNewStateInput" placeholder="Enter new state name">
                    <button type="button" id="editAddStateBtn">Add</button>
                    <span id="editStateError" class="error-message"></span>
                </div>
                <label for="editCity">City:</label>
                <select id="editCity" name="city">
                    <option value="">Select City</option>
                    <option value="new">+ Add New City</option>
                </select>
                <span id="editCityError" class="error-message"></span>
                <div id="editCityForm" class="inline-form">
                    <input type="text" id="editNewCityInput" placeholder="Enter new city name">
                    <button type="button" id="editAddCityBtn">Add</button>
                    <span id="editCityError" class="error-message"></span>
                </div>
                <label for="editBrand">Brand:</label>
                <select id="editBrand" name="brand">
                    <option value="">Select Brand</option>
                    <?php foreach ($brands as $brand): ?>
                        <option value="<?php echo htmlspecialchars($brand['Brand_ID']); ?>">
                            <?php echo htmlspecialchars($brand['Brand_Name']); ?>
                        </option>
                    <?php endforeach; ?>
                    <option value="new">+ Add New Brand</option>
                </select>
                <div id="editBrandForm" class="inline-form">
                    <input type="text" id="editNewBrandInput" placeholder="Enter new brand name">
                    <button type="button" id="editAddBrandBtn">Add</button>
                    <span id="editBrandError" class="error-message"></span>
                </div>
                <label for="editAddress">Street:</label>
                <input type="text" id="editAddress" name="street" placeholder="Enter dealer's street">
                <span id="editStreetError" class="error-message"></span>
                <label for="editPostalCode">Postal Code:</label>
                <input type="text" id="editPostalCode" name="postal_code" placeholder="Enter postal code">
                <span id="editPostalCodeError" class="error-message"></span>
                <label for="editGST">GST Number:</label>
                <input type="text" id="editGST" name="gst_number" placeholder="Enter dealer's GST number">
                <span id="editGSTError" class="error-message"></span>
                <label for="editPAN">PAN Number:</label>
                <input type="text" id="editPAN" name="pan_number" placeholder="Enter dealer's PAN number">
                <span id="editPANError" class="error-message"></span>
                <label for="editAccountStatus">Account Status:</label>
                <select id="editAccountStatus" name="account_status">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
                <button type="button" id="submitEditDealerForm">Save Changes</button>
            </form>
        </div>
    </div>

    <!-- View Dealer Modal -->
    <div id="viewUserModal" class="modal" aria-hidden="true">
        <div class="modal-content">
            <span class="close" onclick="closeViewModal()">&times;</span>
            <h2>View Dealer</h2>
            <div id="viewDealerDetails">
                <!-- Dealer details will be populated here -->
            </div>
        </div>
    </div>

    <!-- Modern Pop-up Message -->
    <div id="popupMessage"></div>
    <script src="../../JAVA SCRIPT/admin_manage_dealer.js"></script>
    <script src="../../JAVA SCRIPT/admin_view_dealer.js"></script>
    
    <!--Edit  Dealer Button -->
    <script>
        function openEditModal(dealerId) {
            // Fetch dealer data using AJAX
            fetch(`get_dealer.php?dealer_id=${dealerId}`)
                .then(response => response.json())
                .then(data => {
                    // Populate the form fields with the fetched data
                    document.getElementById('editDealerId').value = data.Dealer_ID;
                    document.getElementById('editDealerName').value = data.Dealer_Name;
                    document.getElementById('editDealerEmail').value = data.Email;
                    document.getElementById('editPhone').value = data.Phone;
                    document.getElementById('editState').value = data.State_ID;
                    document.getElementById('editCity').innerHTML = `<option value="${data.City_ID}">${data.City_Name}</option>`;
                    document.getElementById('editBrand').value = data.Brand_ID;
                    document.getElementById('editAddress').value = data.Street_Address;
                    document.getElementById('editPostalCode').value = data.Postal_Code;
                    document.getElementById('editGST').value = data.GST_Number;
                    document.getElementById('editPAN').value = data.PAN_Number;
                    document.getElementById('editAccountStatus').value = data.Account_Status;
                    
                    // Show the edit modal
                    document.getElementById('editUserModal').style.display = 'block';
                })
                .catch(error => console.error('Error fetching dealer data:', error));
        }

        function closeEditModal() {
            document.getElementById('editUserModal').style.display = 'none';
        }

        document.getElementById('filterId').addEventListener('input', filterDealers);
        document.getElementById('filterName').addEventListener('input', filterDealers);
        document.getElementById('filterStatus').addEventListener('change', filterDealers);

        function filterDealers() {
            const filterId = document.getElementById('filterId').value.toLowerCase();
            const filterName = document.getElementById('filterName').value.toLowerCase();
            const filterStatus = document.getElementById('filterStatus').value.toLowerCase();
            const rows = document.querySelectorAll('#dealerTableBody tr');

            rows.forEach(row => {
                const id = row.cells[0].textContent.toLowerCase();
                const name = row.cells[1].textContent.toLowerCase();
                const status = row.cells[4].textContent.toLowerCase();

                if ((id.includes(filterId) || !filterId) &&
                    (name.includes(filterName) || !filterName) &&
                    (status.includes(filterStatus) || !filterStatus)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        document.getElementById('state').addEventListener('change', function() {
            const stateId = this.value;
            if (stateId) {
                fetch(`get_cities.php?state_id=${stateId}`)
                    .then(response => {
                        if (!response.ok) {
                            return response.text().then(text => { throw new Error(text) });
                        }
                        return response.json();
                    })
                    .then(data => {
                        const citySelect = document.getElementById('city');
                        citySelect.innerHTML = '<option value="">Select City</option>';
                        data.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.City_ID;
                            option.textContent = city.City_Name;
                            citySelect.appendChild(option);
                        });
                        citySelect.innerHTML += '<option value="new">+ Add New City</option>';
                    })
                    .catch(error => {
                        console.error('Error fetching cities:', error);
                        alert('Error fetching cities: ' + error.message);
                    });
            } else {
                document.getElementById('city').innerHTML = '<option value="">Select City</option><option value="new">+ Add New City</option>';
            }
        });

        document.getElementById('editState').addEventListener('change', function() {
            const stateId = this.value;
            if (stateId) {
                fetch(`get_cities.php?state_id=${stateId}`)
                    .then(response => {
                        if (!response.ok) {
                            return response.text().then(text => { throw new Error(text) });
                        }
                        return response.json();
                    })
                    .then(data => {
                        const citySelect = document.getElementById('editCity');
                        citySelect.innerHTML = '<option value="">Select City</option>';
                        data.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.City_ID;
                            option.textContent = city.City_Name;
                            citySelect.appendChild(option);
                        });
                        citySelect.innerHTML += '<option value="new">+ Add New City</option>';
                    })
                    .catch(error => {
                        console.error('Error fetching cities:', error);
                        alert('Error fetching cities: ' + error.message);
                    });
            } else {
                document.getElementById('editCity').innerHTML = '<option value="">Select City</option><option value="new">+ Add New City</option>';
            }
        });
    </script>
</body>
</html>