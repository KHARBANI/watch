/*====================================== MANGAMENT OF ORDERS ======================================*/

// JavaScript code for managing orders

function filterOrders() {
    const filter = document.getElementById("statusFilter").value;
    const table = document.getElementById("productTableBody");
    const rows = table.getElementsByTagName("tr");

    for (let i = 0; i < rows.length; i++) {
        const statusCell = rows[i].getElementsByTagName("td")[5]; // Status is in the 6th column
        if (statusCell) {
            const status = statusCell.textContent || statusCell.innerText;
            if (filter === "All" || status === filter) {
                rows[i].style.display = ""; // Show the row
            } else {
                rows[i].style.display = "none"; // Hide the row
            }
        }
    }
}

// Function to view order details

document.addEventListener("DOMContentLoaded", function() {
    
    // Function to view order details
    function viewOrder(id) {
        // Fetch customer details (this is a placeholder, implement AJAX call if needed)
        const orderData = {
            1: { customer: "John Doe", product: "Balmain", quantity: "1", totalPrice: "100000", status: "Delivered"},
            2: { customer: "John Doe", product: "Aigner", quantity: "1", totalPrice: "100000", status: "Shipped"},
            3: { customer: "Jane Smith", product: "Aigner", quantity: "2", totalPrice: "200000", status: "Processing"},
            4: { customer: "Jenny Smith", product: "Aigner", quantity: "2", totalPrice: "200000", status: "Pending"},
        };

        const order = orderData[id];
        if (order) {
            document.getElementById("modalContent").innerHTML = `
                <h3>Order Information</h3>
                <p><strong>Customer Name:</strong> ${order.customer}</p>
                <p><strong>Product Name:</strong> ${order.product}</p>
                <p><strong>Quantity:</strong> ${order.quantity}</p>
                <p><strong>Total Price:</strong> ${order.totalPrice}</p>
                <p><strong>Status:</strong> ${order.status}</p>
            `;
            document.getElementById("modal").style.display = "block";
        }
    }

    // Function to close the modal
    function closeInfoModal() {
        document.getElementById("modal").style.display = "none";
    }

    // Expose functions to the global scope
    window.viewOrder = viewOrder;
    window.closeInfoModal = closeInfoModal;
});




/*========================================= MANAGE RPRODUCTS ===========================================*/

// Existing JavaScript code...

// Function to filter products
// JavaScript code for dealer dashboard management
document.addEventListener('DOMContentLoaded', function() {
    const productFilter = document.getElementById('productFilter');
    const minPriceFilter = document.getElementById('minPriceFilter');
    const maxPriceFilter = document.getElementById('maxPriceFilter');
    const stockFilter = document.getElementById('stockFilter');
    const productTableBody = document.getElementById('productTableBody');

    function filterProducts() {
        const productName = productFilter.value.toLowerCase();
        const minPrice = parseFloat(minPriceFilter.value) || 0;
        const maxPrice = parseFloat(maxPriceFilter.value) || Infinity;
        const stockStatus = stockFilter.value;

        const rows = productTableBody.getElementsByTagName('tr');
        for (let row of rows) {
            const cells = row.getElementsByTagName('td');
            const name = cells[1].textContent.toLowerCase();
            const price = parseFloat(cells[3].textContent);
            const stock = parseInt(cells[4].textContent);

            const matchesName = name.includes(productName);
            const matchesPrice = price >= minPrice && price <= maxPrice;
            const matchesStock = stockStatus === "" || (stockStatus === "1" && stock > 0) || (stockStatus === "0" && stock === 0);

            if (matchesName && matchesPrice && matchesStock) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    }

    productFilter.addEventListener('input', filterProducts);
    minPriceFilter.addEventListener('input', filterProducts);
    maxPriceFilter.addEventListener('input', filterProducts);
    stockFilter.addEventListener('change', filterProducts);
});
