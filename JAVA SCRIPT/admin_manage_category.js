document.addEventListener('DOMContentLoaded', function() {
    const addCategoryForm = document.getElementById('addCategoryForm');
    const editCategoryForm = document.getElementById('editCategoryForm');
    const addCategoryBtn = document.getElementById('addCategoryBtn');
    const addCategoryModal = document.getElementById('addCategoryModal');
    const editCategoryModal = document.getElementById('editCategoryModal');
    const closeModalBtns = document.querySelectorAll('.close');

    if (addCategoryForm) {
        addCategoryForm.addEventListener('submit', function(event) {
            const categoryName = document.getElementById('categoryName').value.trim();
            const namePattern = /^[A-Za-z\s]+$/; // Updated pattern to accept spaces
            if (categoryName === '') {
                showPopupMessage('Category name is required.', 'error');
                event.preventDefault();
            } else if (!namePattern.test(categoryName)) {
                showPopupMessage('Category name must contain only letters and spaces.', 'error');
                event.preventDefault();
            }
        });
    }

    if (editCategoryForm) {
        editCategoryForm.addEventListener('submit', function(event) {
            const editCategoryName = document.getElementById('editCategoryName').value.trim();
            const namePattern = /^[A-Za-z\s]+$/; // Updated pattern to accept spaces
            if (editCategoryName === '') {
                showPopupMessage('Category name is required.', 'error');
                event.preventDefault();
            } else if (!namePattern.test(editCategoryName)) {
                showPopupMessage('Category name must contain only letters and spaces.', 'error');
                event.preventDefault();
            }
        });
    }

    if (addCategoryBtn) {
        addCategoryBtn.addEventListener('click', () => {
            addCategoryModal.style.display = 'block';
        });
    }

    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', closeModal);
    });

    window.addEventListener('click', (event) => {
        if (event.target === addCategoryModal || event.target === editCategoryModal) {
            closeModal();
        }
    });

    function closeModal() {
        addCategoryModal.style.display = 'none';
        editCategoryModal.style.display = 'none';
        if (addCategoryForm) addCategoryForm.reset();
        if (editCategoryForm) editCategoryForm.reset();
    }

    function showPopupMessage(message, type, callback) {
        const popupMessage = document.getElementById('popupMessage');
        popupMessage.innerHTML = message;
        popupMessage.className = type;
        popupMessage.style.display = 'block';
        setTimeout(() => {
            popupMessage.style.display = 'none';
            if (callback) callback();
        }, 3000);
    }
});

function viewCategory(categoryId) {
    fetch(`view_category.php?id=${categoryId}`)
        .then(response => response.text())
        .then(data => {
            const modalContent = document.getElementById('modalContent');
            modalContent.innerHTML = data;
            const modal = document.getElementById('modal');
            modal.style.display = 'block';
        })
        .catch(error => console.error('Error:', error));
}

function openEditModal(categoryId) {
    fetch(`get_category.php?id=${categoryId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('editCategoryName').value = data.Category_Name;
            document.getElementById('editCategoryStatus').value = data.Category_Status;
            document.getElementById('editCategoryForm').action = `update_category.php?id=${categoryId}`;
            const editCategoryModal = document.getElementById('editCategoryModal');
            editCategoryModal.style.display = 'block';
        })
        .catch(error => console.error('Error:', error));
}

document.getElementById('editCategoryForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);
    const categoryId = new URLSearchParams(this.action.split('?')[1]).get('id');

    fetch(`update_category.php?id=${categoryId}`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data.includes('success')) {
            window.location.reload();
        } else {
            showPopupMessage(data, 'error');
        }
    })
    .catch(error => console.error('Error:', error));
});

function toggleCategoryStatus(categoryId, currentStatus) {
    const newStatus = currentStatus === 'Active' ? 'Inactive' : 'Active';

    fetch(`toggle_category_status.php?id=${categoryId}&status=${newStatus}`, {
        method: 'POST'
    })
    .then(response => response.text())
    .then(data => {
        if (data.includes('success')) {
            window.location.reload();
        } else {
            showPopupMessage(data, 'error');
        }
    })
    .catch(error => console.error('Error:', error));
}

function closeInfoModal() {
    const modal = document.getElementById('modal');
    modal.style.display = 'none';
}