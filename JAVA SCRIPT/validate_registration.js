function validateForm() {
    let isValid = true;

    // Clear previous error messages
    document.getElementById('nameError').innerText = '';
    document.getElementById('emailError').innerText = '';
    document.getElementById('phoneError').innerText = '';
    document.getElementById('passwordError').innerText = '';
    document.getElementById('confirmPasswordError').innerText = '';

    // Validate Name
    const name = document.getElementById('name').value;
    if (name.trim() === '') {
        document.getElementById('nameError').innerText = 'Name is required';
        isValid = false;
    }

    // Validate Email
    const email = document.getElementById('email').value;
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        document.getElementById('emailError').innerText = 'Invalid email format';
        isValid = false;
    }

    // Validate Phone Number
    const phone = document.getElementById('phone').value;
    if (phone.trim() === '') {
        document.getElementById('phoneError').innerText = 'Phone number is required';
        isValid = false;
    }

    // Validate Password
    const password = document.getElementById('password').value;
    if (password.length < 6) {
        document.getElementById('passwordError').innerText = 'Password must be at least 6 characters';
        isValid = false;
    }

    // Validate Confirm Password
    const confirmPassword = document.getElementById('confirmPassword').value;
    if (password !== confirmPassword) {
        document.getElementById('confirmPasswordError').innerText = 'Passwords do not match';
        isValid = false;
    }

    return isValid;
}

function openRegisterPopup() {
    document.getElementById('registerPopup').style.display = 'block';
}

function closeRegisterPopup() {
    document.getElementById('registerPopup').style.display = 'none';
}