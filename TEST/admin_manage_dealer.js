// DOM Elements
const addUserBtn = document.getElementById('addUserBtn');
const addUserModal = document.getElementById('addUserModal');
const closeModalBtn = document.querySelector('.close');
const addDealerForm = document.getElementById('addDealerForm');

// Open the Add User Modal
addUserBtn.addEventListener('click', () => {
    addUserModal.style.display = 'block';
});

// Close the Modal
closeModalBtn.addEventListener('click', closeModal);
window.addEventListener('click', (event) => {
    if (event.target === addUserModal) {
        closeModal();
    }
});

function closeModal() {
    addUserModal.style.display = 'none';
    addUserForm.reset();
}

function closeEditModal() {
    editUserModal.style.display = 'none';
    editUserForm.reset();
}

// Validate the Add User Form
addUserForm.onsubmit = function(event) {
    event.preventDefault(); // Prevent form submission

    const userName = document.getElementById('userName').value.trim();
    const userEmail = document.getElementById('userEmail').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const state = document.getElementById('state').value.trim();
    const city = document.getElementById('city').value.trim();
    const address = document.getElementById('address').value.trim();
    const brand = document.getElementById('brand').value.trim();
    const gst = document.getElementById('GST').value.trim();
    const pan = document.getElementById('PAN').value.trim();

    // Enhanced validation
    if (!userName || !userEmail || !phone || !state || !city || !address || !brand || !gst || !pan) {
        alert("All fields are required.");
        return;
    }

    // Validate Username
    if (!userName || /[^a-zA-Z]/.test(userName)) {
        document.getElementById('nameError').textContent="Username is required and should not contain special characters and numbers.";
        return;
    }

    // Email validation
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(userEmail)) {
        alert("Please enter a valid email address.");
        return;
    }

    // Phone validation (example: must be 10 digits)
    const phonePattern = /^\d{10}$/;
    if (!phonePattern.test(phone)) {
        alert("Please enter a valid 10-digit phone number.");
        return;
    }

    // Validate State
    if (!state || /[^a-zA-Z ]/.test(state)) {
        alert("State is required and should only contain letters.");
        return;
    }

    // Validate City
    if (!city || /[^a-zA-Z ]/.test(city)) {
        alert("City is required and should only contain letters.");
        return;
    }

    // Validate Address
    if (!address) {
        alert("Address is required.");
        return;
    }

    // Validate Brand
    if (!brand) {
        alert("Brand is required.");
        return;
    }

    // Validate GST Number (example pattern)
    const gstPattern = /^([0-9]{2})([A-Z]{5})([0-9]{4})([A-Z]{1})([0-9]{1})([Z]{1})([0-9A-Z]{1})$/;
    if (!gstPattern.test(gst)) {
        alert("Please enter a valid GST number.");
        return;
    }

    // Validate PAN Number (example pattern)
    const panPattern = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
    if (!panPattern.test(pan)) {
        alert("Please enter a valid PAN number.");
        return;
    }
};

function closeInfoModal() {
    document.getElementById("modal").style.display = "none";
}

/* Last updated */
