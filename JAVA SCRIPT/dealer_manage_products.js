function openAddProductModal() {
    const modal = document.getElementById('addProductModal');
    modal.style.display = 'block';
    modal.removeAttribute('inert');
}

function closeAddProductModal() {
    const modal = document.getElementById('addProductModal');
    modal.style.display = 'none';
    modal.setAttribute('inert', '');
}

function closeEditProductModal() {
    const modal = document.getElementById('editProductModal');
    modal.style.display = 'none';
    modal.setAttribute('inert', '');
}

function closeAddStockModal() {
    const modal = document.getElementById('addStockModal');
    modal.style.display = 'none';
    modal.setAttribute('inert', '');
}

function showNotification(message, type = 'error', reload = false) {
    const popupMessage = document.getElementById('popupMessage');
    popupMessage.innerText = message;
    popupMessage.className = type;
    popupMessage.style.display = 'block';
    setTimeout(() => {
        popupMessage.style.display = 'none';
        if (reload) {
            location.reload(); // Reload the page after the timeout
        }
    }, 3000); // Display notification for 3 seconds
}

document.getElementById('caseMaterial').addEventListener('change', function() {
    const newInput = document.getElementById('newCaseMaterialInput');
    if (this.value === 'new') {
        newInput.style.display = 'block';
        this.parentNode.insertBefore(newInput, this.nextSibling);
    } else {
        newInput.style.display = 'none';
    }
});

document.getElementById('strapMaterial').addEventListener('change', function() {
    const newInput = document.getElementById('newStrapMaterialInput');
    if (this.value === 'new') {
        newInput.style.display = 'block';
        this.parentNode.insertBefore(newInput, this.nextSibling);
    } else {
        newInput.style.display = 'none';
    }
});

document.getElementById('movementType').addEventListener('change', function() {
    const newInput = document.getElementById('newMovementTypeInput');
    if (this.value === 'new') {
        newInput.style.display = 'block';
        this.parentNode.insertBefore(newInput, this.nextSibling);
    } else {
        newInput.style.display = 'none';
    }
});

document.getElementById('editCaseMaterial').addEventListener('change', function() {
    const newInput = document.getElementById('newCaseMaterialInput');
    if (this.value === 'new') {
        newInput.style.display = 'block';
        this.parentNode.insertBefore(newInput, this.nextSibling);
    } else {
        newInput.style.display = 'none';
    }
});

document.getElementById('editStrapMaterial').addEventListener('change', function() {
    const newInput = document.getElementById('newStrapMaterialInput');
    if (this.value === 'new') {
        newInput.style.display = 'block';
        this.parentNode.insertBefore(newInput, this.nextSibling);
    } else {
        newInput.style.display = 'none';
    }
});

document.getElementById('editMovementType').addEventListener('change', function() {
    const newInput = document.getElementById('newMovementTypeInput');
    if (this.value === 'new') {
        newInput.style.display = 'block';
        this.parentNode.insertBefore(newInput, this.nextSibling);
    } else {
        newInput.style.display = 'none';
    }
});

function addNewCaseMaterial() {
    let newCaseMaterial = document.getElementById('newCaseMaterialText').value;
    if (newCaseMaterial) {
        // Check for duplicate entry
        fetch(`check_material.php?type=case&name=${encodeURIComponent(newCaseMaterial)}`)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    showNotification('Case material already exists', 'error');
                } else {
                    // Add new case material
                    fetch('add_material.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `type=case&name=${encodeURIComponent(newCaseMaterial)}`
                    })
                    .then(response => response.text())
                    .then(text => {
                        try {
                            const data = JSON.parse(text);
                            showNotification(data.message, data.success ? 'success' : 'error');
                            if (data.success) {
                                let option = new Option(newCaseMaterial, data.id);
                                document.getElementById('caseMaterial').add(option, undefined);
                                document.getElementById('caseMaterial').value = data.id;
                                document.getElementById('newCaseMaterialInput').style.display = 'none';
                            }
                        } catch (error) {
                            console.error('Error parsing JSON:', error, text);
                            alert('Failed to add case material: Invalid server response');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to add case material: ' + error.message);
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to check case material: ' + error.message);
            });
    } else {
        showNotification('Case material cannot be empty', 'error');
    }
}

function addNewStrapMaterial() {
    let newStrapMaterial = document.getElementById('newStrapMaterialText').value;
    if (newStrapMaterial) {
        // Check for duplicate entry
        fetch(`check_material.php?type=strap&name=${encodeURIComponent(newStrapMaterial)}`)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    showNotification('Strap material already exists', 'error');
                } else {
                    // Add new strap material
                    fetch('add_material.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `type=strap&name=${encodeURIComponent(newStrapMaterial)}`
                    })
                    .then(response => response.text())
                    .then(text => {
                        try {
                            const data = JSON.parse(text);
                            showNotification(data.message, data.success ? 'success' : 'error');
                            if (data.success) {
                                let option = new Option(newStrapMaterial, data.id);
                                document.getElementById('strapMaterial').add(option, undefined);
                                document.getElementById('strapMaterial').value = data.id;
                                document.getElementById('newStrapMaterialInput').style.display = 'none';
                            }
                        } catch (error) {
                            console.error('Error parsing JSON:', error, text);
                            alert('Failed to add strap material: Invalid server response');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to add strap material: ' + error.message);
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to check strap material: ' + error.message);
            });
    } else {
        showNotification('Strap material cannot be empty', 'error');
    }
}

function addNewMovementType() {
    let newMovementType = document.getElementById('newMovementTypeText').value;
    if (newMovementType) {
        // Check for duplicate entry
        fetch(`check_material.php?type=movement&name=${encodeURIComponent(newMovementType)}`)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    showNotification('Movement type already exists', 'error');
                } else {
                    // Add new movement type
                    fetch('add_material.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `type=movement&name=${encodeURIComponent(newMovementType)}`
                    })
                    .then(response => response.text())
                    .then(text => {
                        try {
                            const data = JSON.parse(text);
                            showNotification(data.message, data.success ? 'success' : 'error');
                            if (data.success) {
                                let option = new Option(newMovementType, data.id);
                                document.getElementById('movementType').add(option, undefined);
                                document.getElementById('movementType').value = data.id;
                                document.getElementById('newMovementTypeInput').style.display = 'none';
                            }
                        } catch (error) {
                            console.error('Error parsing JSON:', error, text);
                            alert('Failed to add movement type: Invalid server response');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to add movement type: ' + error.message);
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to check movement type: ' + error.message);
            });
    } else {
        showNotification('Movement type cannot be empty', 'error');
    }
}

// Handle product addition without redirection
document.getElementById('addProductForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);
    fetch(this.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        try {
            const data = JSON.parse(text);
            if (data.success) {
                showNotification(`Product added successfully. Product ID: ${data.watch_id}`, 'success', true); // Show product ID and reload
                closeAddProductModal();
            } else {
                showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Error parsing JSON:', error, text);
            alert('Failed to add product: Invalid server response');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to add product: ' + error.message);
    });
});

// Handle product editing without redirection
document.getElementById('editProductForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);
    fetch(this.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        try {
            const data = JSON.parse(text);
            if (data.success) {
                showNotification('Product updated successfully', 'success');
                closeEditProductModal();
                location.reload();
            } else {
                showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Error parsing JSON:', error, text);
            alert('Failed to update product: Invalid server response');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update product: ' + error.message);
    });
});

function editProduct(productId) {
    // Fetch product details using AJAX
    fetch(`view_product.php?id=${productId}`)
        .then(response => response.json())
        .then(data => {
            // Populate edit modal with product details
            document.getElementById('editProductId').value = data.id;
            document.getElementById('editProductName').value = data.name;
            document.getElementById('editProductPrice').value = data.price;
            document.getElementById('editProductStock').value = data.stock; // Ensure stock quantity is populated
            document.getElementById('editProductDescription').value = data.description;

            // Fetch and populate case materials
            fetch('fetch_case_materials.php')
                .then(response => response.json())
                .then(caseMaterials => {
                    const caseMaterialSelect = document.getElementById('editCaseMaterial');
                    caseMaterialSelect.innerHTML = '<option value="" disabled selected hidden>Select Case Material</option>';
                    caseMaterials.forEach(material => {
                        const option = document.createElement('option');
                        option.value = material.Case_ID;
                        option.textContent = material.Case_Name;
                        if (material.Case_Name === data.case_material) {
                            option.selected = true;
                        }
                        caseMaterialSelect.appendChild(option);
                    });
                    const newOption = document.createElement('option');
                    newOption.value = 'new';
                    newOption.textContent = 'Add New Case Material';
                    caseMaterialSelect.appendChild(newOption);
                });

            // Fetch and populate strap materials
            fetch('fetch_strap_materials.php')
                .then(response => response.json())
                .then(strapMaterials => {
                    const strapMaterialSelect = document.getElementById('editStrapMaterial');
                    strapMaterialSelect.innerHTML = '<option value="" disabled selected hidden>Select Strap Material</option>';
                    strapMaterials.forEach(material => {
                        const option = document.createElement('option');
                        option.value = material.Strap_ID;
                        option.textContent = material.Strap_Name;
                        if (material.Strap_Name === data.strap_material) {
                            option.selected = true;
                        }
                        strapMaterialSelect.appendChild(option);
                    });
                    const newOption = document.createElement('option');
                    newOption.value = 'new';
                    newOption.textContent = 'Add New Strap Material';
                    strapMaterialSelect.appendChild(newOption);
                });

            // Fetch and populate movement types
            fetch('fetch_movement_types.php')
                .then(response => response.json())
                .then(movementTypes => {
                    const movementTypeSelect = document.getElementById('editMovementType');
                    movementTypeSelect.innerHTML = '<option value="" disabled selected hidden>Select Movement Type</option>';
                    movementTypes.forEach(type => {
                        const option = document.createElement('option');
                        option.value = type.Movement_ID;
                        option.textContent = type.Movement_Name;
                        if (type.Movement_Name === data.movement_type) {
                            option.selected = true;
                        }
                        movementTypeSelect.appendChild(option);
                    });
                    const newOption = document.createElement('option');
                    newOption.value = 'new';
                    newOption.textContent = 'Add New Movement Type';
                    movementTypeSelect.appendChild(newOption);
                });

            // Display the edit modal
            const editModal = document.getElementById('editProductModal');
            editModal.style.display = 'block';
            editModal.removeAttribute('inert');
        })
        .catch(error => console.error('Error fetching product details:', error));
}

// Ensure the function is available globally
window.editProduct = editProduct;