function viewCategory(categoryId) {
    // Sample product details for demonstration
    const productDetails = {
        1: { name: "Amazefit", description: "Smartwatch with fitness tracking features." },
        2: { name: "Aigner", description: "Luxury watch with elegant design." }
    };

    // Get the modal and modal content elements
    const modal = document.getElementById('modal');
    const modalContent = document.getElementById('modalContent');

    // Check if the categoryId exists in the productDetails
    if (productDetails[categoryId]) {
        const product = productDetails[categoryId];
        modalContent.innerHTML = `<h3>${product.name}</h3><p>${product.description}</p>`;
        modal.style.display = "block"; // Show the modal
    } else {
        modalContent.innerHTML = "<p>Product details not found.</p>";
        modal.style.display = "block"; // Show the modal
    }
}

// Function to close the modal
function closeInfoModal() {
    const modal = document.getElementById('modal');
    modal.style.display = "none"; // Hide the modal
}
