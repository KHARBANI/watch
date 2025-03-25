function viewReview(id) {
    // Fetch customer details (this is a placeholder, implement AJAX call if needed)
    const reviewData = {
        1: { name: "Product 1", customer: "Jane Smith", rating: "5", comment: "Great Product!", date: "2024-01-01"},
        2: { name: "Product 2", customer: "Jenny Smith", rating: "4", comment: "Good product!", date: "2024-02-04"}
    };
    
    const review = reviewData[id];
    if (review) {
        document.getElementById("modalContent").innerHTML = `
            <h3>Review Information</h3>
            <p><strong>Product Name:</strong> ${review.name}</p>
            <p><strong>Customer Name:</strong> ${review.customer}</p>
            <p><strong>Rating:</strong> ${review.rating}</p>
            <p><strong>Comment:</strong> ${review.comment}</p>
            <p><strong>Date:</strong> ${review.date}</p>
        `;
        document.getElementById("modal").style.display = "block";
    }
}

function closeInfoModal() {
    document.getElementById("modal").style.display = "none";
}