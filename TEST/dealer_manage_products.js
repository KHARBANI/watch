// JavaScript for Dealer Manage Products

let products = [
    { id: 1, name: "Amazefit Unisex GTS 4 Mini", category: "Amazefit", price: 10000, stock: 10, active: true },
    { id: 2, name: "Amazefit POR 3R", category: "Amazefit", price: 20000, stock: 20, active: true }
];

function loadProducts() {
    const storedProducts = localStorage.getItem('products');
    if (storedProducts) {
        products = JSON.parse(storedProducts);
        updateProductTable();
    }
}

function addProduct(event) {
    event.preventDefault(); // Prevent the default form submission

    // Get the values from the input fields
    const productName = document.getElementById('productName').value;
    const productPrice = document.getElementById('productPrice').value;
    const productStock = document.getElementById('productStock').value;

    // Create a new product object with category set to "Amazefit"
    const newProduct = {
        id: products.length + 1,
        name: productName,
        category: "Amazefit",
        price: productPrice,
        stock: productStock,
        active: true // Set active status
    };

    // Add the new product to the array
    products.push(newProduct);

    // Save products to local storage
    localStorage.setItem('products', JSON.stringify(products));

    // Update the product table
    updateProductTable();

    // Close the modal
    closeAddProductModal();
}

function viewProduct(id) {
    const product = products.find(p => p.id === id);
    const productDetails = `
        <strong>ID:</strong> ${product.id}<br>
        <strong>Name:</strong> ${product.name}<br>
        <strong>Category:</strong> ${product.category}<br>
        <strong>Price:</strong> ${product.price}<br>
        <strong>Stock:</strong> ${product.stock}
    `;
    document.getElementById('modalContent').innerHTML = productDetails;
    document.getElementById('modal').style.display = 'block';
}

function editProduct(id) {
    const product = products.find(p => p.id === id);
    document.getElementById('editProductName').value = product.name;
    document.getElementById('editProductCategory').value = "Amazefit"; // Automatically set category to "Amazefit"
    document.getElementById('editProductPrice').value = product.price;
    document.getElementById('editProductStock').value = product.stock;
    document.getElementById('editProductModal').style.display = 'block'; // Open the edit modal
}

function updateEditedProduct(event) {
    event.preventDefault(); // Prevent the default form submission

    // Get the values from the edit input fields
    const productName = document.getElementById('editProductName').value;
    const productPrice = document.getElementById('editProductPrice').value;
    const productStock = document.getElementById('editProductStock').value;

    // Find the product to update
    const productId = products.findIndex(p => p.name === productName); // Assuming name is unique
    products[productId] = {
        id: products[productId].id,
        name: productName,
        category: "Amazefit", // Set category to "Amazefit"
        price: productPrice,
        stock: productStock,
        active: true // Ensure the product remains active
    };

    // Save updated products to local storage
    localStorage.setItem('products', JSON.stringify(products));

    // Update the product table
    updateProductTable();

    // Close the edit modal
    closeEditProductModal();
}

function toggleProductStatus(id) {
    const product = products.find(p => p.id === id);
    product.active = !product.active; // Toggle active status
    const action = product.active ? 'activated' : 'deactivated';
    alert(`Product ID: ${id} has been ${action}.`);
    updateProductTable(); // Update the product table to reflect changes
}

function closeInfoModal() {
    document.getElementById('modal').style.display = 'none';
}

function closeEditProductModal() {
    document.getElementById('editProductModal').style.display = 'none';
}

function openAddProductModal() {
    console.log("Add Product modal opened");
    document.getElementById('addProductModal').style.display = 'block';
}

function closeAddProductModal() {
    document.getElementById('addProductModal').style.display = 'none';
}

function updateProductTable() {
    const tbody = document.getElementById('productTableBody');
    tbody.innerHTML = '';
    products.forEach(product => {
        const row = `<tr>
            <td>${product.id}</td>
            <td>${product.name}</td>
            <td>${product.category}</td>
            <td>${product.price}</td>
            <td>${product.stock}</td>
            <td>
                <button class="action-btn view-btn" onclick="viewProduct(${product.id})" aria-label="View ${product.name} product">View</button>
                <button class="action-btn edit-btn" onclick="editProduct(${product.id})" aria-label="Edit ${product.name} product">Edit</button>
                <button class="action-btn delete-btn" onclick="toggleProductStatus(${product.id})" aria-label="Toggle ${product.name} product status">${product.active ? 'Deactivate' : 'Activate'}</button>
            </td>
        </tr>`;
        tbody.innerHTML += row;
    });
}

document.addEventListener('DOMContentLoaded', function() {
    loadProducts(); // Load products from local storage
    document.getElementById('addProductForm').onsubmit = addProduct;
    document.getElementById('editProductForm').onsubmit = updateEditedProduct; // Bind the edit form submission
});
