document.getElementById('see-more').addEventListener('click', function() {
    const productDescription = document.getElementById('product-description');
    productDescription.style.maxHeight = 'none'; // Remove max height to show all content
    this.style.display = 'none'; // Hide the "See More" button
});