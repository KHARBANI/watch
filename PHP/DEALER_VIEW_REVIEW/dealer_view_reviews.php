<?php
// Database connection
require_once('../../PHP/db_connection.php');

session_start();

if (isset($_SESSION['dealer_id'])) {
    $dealer_id = intval($_SESSION['dealer_id']);

    // Fetch review data with product name and customer name
    $sql = "SELECT r.Review_ID, c.Customer_Name, p.Model_Name, r.Review_Details, r.Review_Date 
            FROM review_table r
            JOIN customer_table c ON r.Customer_ID = c.Customer_ID
            JOIN product_table p ON r.Watch_ID = p.Watch_ID
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
    <title>View Reviews</title>
    <link rel="stylesheet" href="../../CSS/admin_dashboard.css">
    <style>
        /* Add CSS for modal */
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
        <section class="manage-reviews">
            <h2>View Reviews</h2>
            <div class="filter-container">
                <input type="text" id="filterReviewID" placeholder="Filter by Review ID">
                <input type="text" id="filterProduct" placeholder="Filter by Product">
                <input type="text" id="filterRating" placeholder="Filter by Rating">
                <input type="date" id="filterDate" placeholder="Filter by Date">
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Review ID</th>
                        <th>Product</th>
                        <th>Customer</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="reviewTableBody">
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['Review_ID']}</td>
                                    <td>{$row['Model_Name']}</td>
                                    <td>{$row['Customer_Name']}</td>
                                    <td>{$row['Review_Details']}</td>
                                    <td>{$row['Review_Date']}</td>
                                    <td>
                                        <button class='action-btn view-btn' onclick='viewReview({$row['Review_ID']})'>View</button>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No reviews found</td></tr>";
                    }

                    $stmt->close();
                    ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Admin Dashboard. All rights reserved.</p>
    </footer>

    <!-- Modal for Review Information -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeInfoModal()">&times;</span>
            <div id="modalContent"></div>
        </div>
    </div>

    <script>
        document.getElementById('filterReviewID').addEventListener('input', filterReviews);
        document.getElementById('filterProduct').addEventListener('input', filterReviews);
        document.getElementById('filterRating').addEventListener('input', filterReviews);
        document.getElementById('filterDate').addEventListener('input', filterReviews);

        function filterReviews() {
            const reviewID = document.getElementById('filterReviewID').value.toLowerCase();
            const product = document.getElementById('filterProduct').value.toLowerCase();
            const rating = document.getElementById('filterRating').value.toLowerCase();
            const date = document.getElementById('filterDate').value.toLowerCase();
            const rows = document.querySelectorAll('#reviewTableBody tr');

            rows.forEach(row => {
                const id = row.cells[0].textContent.toLowerCase();
                const productName = row.cells[1].textContent.toLowerCase();
                const reviewRating = row.cells[3].textContent.toLowerCase();
                const reviewDate = row.cells[5].textContent.toLowerCase();

                if (id.includes(reviewID) && productName.includes(product) && reviewRating.includes(rating) && reviewDate.includes(date)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function viewReview(reviewId) {
            // Fetch review details using AJAX
            fetch(`get_review_details.php?review_id=${reviewId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        return response.text().then(text => {
                            console.error("Received response is not JSON:", text);
                            throw new TypeError("Received response is not JSON");
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    const modalContent = document.getElementById('modalContent');
                    modalContent.innerHTML = `
                        <h2>Review Details</h2>
                        <p><strong>Review ID:</strong> ${data.Review_ID}</p>
                        <p><strong>Customer Name:</strong> ${data.Customer_Name || 'N/A'}</p>
                        <p><strong>Rating:</strong> ${data.Rating}</p>
                        <p><strong>Review Details:</strong> ${data.Review_Details}</p>
                        <p><strong>Review Date:</strong> ${data.Review_Date}</p>
                        <p><strong>Order ID:</strong> ${data.Order_ID}</p>
                        <p><strong>Watch ID:</strong> ${data.Watch_ID}</p>
                    `;
                    document.getElementById('modal').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error fetching review details:', error.message);
                    alert('An error occurred while fetching review details. Please try again later.');
                });
        }

        function closeInfoModal() {
            document.getElementById('modal').style.display = 'none';
        }
    </script>
</body>
</html>
