// Open Add Category Modal
document.getElementById("addCategoryBtn").addEventListener("click", () => {
    document.getElementById("addCategoryModal").style.display = "block";
});

// Close Add Category Modal
function closeAddModal() {
    document.getElementById("addCategoryModal").style.display = "none";
    document.getElementById("addCategoryForm").reset();
}

// Handle Add Category Form Submission
document.getElementById("addCategoryForm").addEventListener("submit", (event) => {
    event.preventDefault();

    const categoryName = document.getElementById("newCategoryName").value.trim();
    const categoryDescription = document.getElementById("newCategoryDescription").value.trim();

    if (!categoryName || !categoryDescription) {
        alert("Please fill in all fields.");
        return;
    }

    // Simulate adding a new category
    const newId = Date.now(); // Generate a unique ID
    const newRow = `
        <tr data-id="${newId}">
            <td>${newId}</td>
            <td>${categoryName}</td>
            <td>${categoryDescription}</td>
            <td>
                <button class="action-btn view-btn" onclick="viewCategory(${newId})">View</button>
                <button class="action-btn edit-btn" onclick="editCategory(${newId})">Edit</button>
                <button class="action-btn deactivate-btn" onclick="deactivateCategory(${newId})">Deactivate</button>
            </td>
        </tr>
    `;
    document.getElementById("categoryTableBody").insertAdjacentHTML("beforeend", newRow);

    // Close the modal
    closeAddModal();
    alert("Category added successfully!");
});