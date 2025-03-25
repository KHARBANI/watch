function viewProduct(id) {
    // Fetch customer details (this is a placeholder, implement AJAX call if needed)
    const productData = {
        1: { name: "Balmain", category: "Balmain", price: "10000", stock: "10" },
        2: { name: "Aigner", category: "Aigner", price: "20000", stock: "20" },
    };

    const product = productData[id];
    if (product) {
        document.getElementById("modalContent").innerHTML = `
            <h3>Product Information</h3>
            <p><strong>Name:</strong> ${product.name}</p>
            <p><strong>Category:</strong> ${product.category}</p>
            <p><strong>Price:</strong> ${product.price}</p>
            <p><strong>Stock:</strong> ${product.stock}</p>
        `;
        document.getElementById("modal").style.display = "block";
    }
}

function closeInfoModal() {
    document.getElementById("modal").style.display = "none";
}