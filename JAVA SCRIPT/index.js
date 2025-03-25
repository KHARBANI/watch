function toggleMenu() {
    const navLinks = document.querySelector('.nav-links');
    navLinks.classList.toggle('active');
}

let currentSlide = 0;

function showSlides() {
    const slides = document.querySelectorAll('.slide');
    slides.forEach((slide, index) => {
        slide.style.display = (index === currentSlide) ? 'block' : 'none';
    });
}

function changeSlide(n) {
    const slides = document.querySelectorAll('.slide');
    currentSlide = (currentSlide + n + slides.length) % slides.length; // This line ensures circular navigation
    showSlides();
}

// Initialize the slider
showSlides();

/*-------------------------------Login--------------------------*/

function openLoginPopup() {
    closeRegisterPopup();
    const loginPopup = document.getElementById('loginPopup');
    loginPopup.style.display = 'flex';
}

// Function to close the login popup
function closeLoginPopup() {
    const loginPopup = document.getElementById('loginPopup');
    loginPopup.style.display = 'none';
}

/* ------------------------- Forgot Password ---------------------- */

function openForgotPasswordPopup() {
    closeLoginPopup(); 
    const forgotPasswordPopup = document.getElementById('forgotPasswordPopup');
    forgotPasswordPopup.style.display = 'flex';

    // Clear the form fields
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    if (forgotPasswordForm) {
        forgotPasswordForm.querySelectorAll('input').forEach(input => input.value = "");
    }
}

// Function to close the forgot password popup
function closeForgotPasswordPopup() {
    const forgotPasswordPopup = document.getElementById('forgotPasswordPopup');
    forgotPasswordPopup.style.display = 'none';
}

/*----------------------------Change Password------------------------ */
function openChangePasswordPopup() {
    closeForgotPasswordPopup(); 
    const changePasswordPopup = document.getElementById('changePasswordPopup');
    changePasswordPopup.style.display = 'flex';
}


function closeChangePasswordPopup() {
    const changePasswordPopup = document.getElementById('changePasswordPopup');
    changePasswordPopup.style.display = 'none';
}


/*---------------------Registration------------------ */
function openRegisterPopup() {
    closeLoginPopup();
    const registerPopup = document.getElementById('registerPopup');
    registerPopup.style.display = 'flex';
}

// Function to close the registration popup
function closeRegisterPopup() {
    const registerPopup = document.getElementById('registerPopup');
    registerPopup.style.display = 'none';
}

/*-------------------Form Validation----------------- */
function validateLoginForm() {
    let email = document.getElementById('loginEmailUnique').value;
    let password = document.getElementById('loginPasswordUnique').value;
    let userType = document.getElementById('usertype').value;
    let isValid = true;

    // Clear previous error messages
    document.getElementById('loginEmailError').innerText = '';
    document.getElementById('loginPasswordError').innerText = '';
    document.getElementById('usertypeError').innerText = '';

    // Validate email
    if (email === '') {
        document.getElementById('loginEmailError').innerText = 'Email is required';
        isValid = false;
    } else if (!validateEmail(email)) {
        document.getElementById('loginEmailError').innerText = 'Invalid email format';
        isValid = false;
    }

    // Validate password
    if (password === '') {
        document.getElementById('loginPasswordError').innerText = 'Password is required';
        isValid = false;
    }

    // Validate user type
    if (userType === 'default') {
        document.getElementById('usertypeError').innerText = 'Please select a user type';
        isValid = false;
    }

    return isValid;
}

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validateForgotPasswordForm() {
    const email = document.getElementById('forgotPasswordEmail').value;
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Clear previous error messages
    document.getElementById('forgotPasswordEmailError').textContent = '';

    if (!email) {
        document.getElementById('forgotPasswordEmailError').textContent = 'Email is required.';
        return false; // Prevent submission
    } else if (!emailPattern.test(email)) {
        document.getElementById('forgotPasswordEmailError').textContent = 'Please enter a valid email address.';
        return false; // Prevent submission
    }

    return true; // Allow submission
}

function validateChangePasswordForm() {
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    // Define password validation pattern
    const passwordPattern = /^(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/;

    // Clear previous error messages
    document.getElementById('newPasswordError').textContent = '';
    document.getElementById('confirmPasswordError').textContent = '';

    let isValid = true;

    // Validate New Password
    if (!newPassword) {
        document.getElementById('newPasswordError').textContent = 'New Password is required.';
        isValid = false;
    } else if (!passwordPattern.test(newPassword)) {
        document.getElementById('newPasswordError').textContent =
            'Password must be at least 8 characters long and include at least one number and one special character.';
        isValid = false;
    }

    // Validate Confirm Password
    if (!confirmPassword) {
        document.getElementById('confirmPasswordError').textContent = 'Confirm Password is required.';
        isValid = false;
    } else if (newPassword !== confirmPassword) {
        document.getElementById('confirmPasswordError').textContent = 'Passwords do not match.';
        isValid = false;
    }

    return isValid; // Return true only if all validations pass
}

function validateRegistrationForm() {
    const name = document.getElementById('nameUnique').value;
    const email = document.getElementById('registerEmailUnique').value;
    const phone = document.getElementById('phoneUnique').value;
    const password = document.getElementById('registerPasswordUnique').value.trim();
    const confirmPassword = document.getElementById('registerConfirmPasswordUnique').value.trim();

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const namePattern = /^[A-Za-z\s]+$/;
    const passwordPattern = /^(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/;

    // Clear previous errors
    document.getElementById('nameError').textContent = '';
    document.getElementById('registerEmailError').textContent = '';
    document.getElementById('phoneError').textContent = '';
    document.getElementById('registerPasswordError').textContent = '';
    document.getElementById('registerConfirmPasswordError').textContent = '';

    let isValid = true;

    if (!name) {
        document.getElementById('nameError').textContent = 'Name is required.';
        isValid = false;
    } else if (name.length < 3) {
        document.getElementById('nameError').textContent = 'Name must be at least 3 characters long.';
        isValid = false;
    } else if (!namePattern.test(name)) {
        document.getElementById('nameError').textContent = 'Name can only contain letters and spaces.';
        isValid = false;
    }

    if (!email) {
        document.getElementById('registerEmailError').textContent = 'Email is required.';
        isValid = false;
    } else if (!emailPattern.test(email)) {
        document.getElementById('registerEmailError').textContent = 'Please enter a valid email address.';
        isValid = false;
    }

    if (!phone) {
        document.getElementById('phoneError').textContent = 'Phone number is required.';
        isValid = false;
    } else if (!phone.match(/^\d{10}$/)) {
        document.getElementById('phoneError').textContent = 'Please enter a valid 10-digit phone number.';
        isValid = false;
    }

    if (!password) {
        document.getElementById('registerPasswordError').textContent = 'Password is required.';
        isValid = false;
    } else if (!passwordPattern.test(password)) {
        document.getElementById('registerPasswordError').textContent =
            'Password must be at least 8 characters long and include at least one number and one special character.';
        isValid = false;
    }

    if (!confirmPassword) {
        document.getElementById('registerConfirmPasswordError').textContent = 'Confirm Password is required.';
        isValid = false;
    } else if (password !== confirmPassword) {
        document.getElementById('registerConfirmPasswordError').textContent = 'Passwords do not match.';
        isValid = false;
    }

    return isValid;
}

// Ensure event listeners are set up after DOM is fully loaded
document.addEventListener('DOMContentLoaded', function () {
    // Attach validation to form submissions
    document.querySelector('#loginPopup form').onsubmit = function (event) {
        if (!validateLoginForm()) {
            event.preventDefault(); // Prevent form submission if validation fails
        }
    };

    document.querySelector('#forgotPasswordPopup form').onsubmit = function (event) {
        event.preventDefault(); // Always prevent default form submission

        // Validate the form
        if (validateForgotPasswordForm()) {
            // Close the "Forgot Password" popup
            closeForgotPasswordPopup();

            // Open the "Change Password" popup
            openChangePasswordPopup();
        }
    };

    document.querySelector('#registerPopup form').onsubmit = function (event) {
        if (!validateRegistrationForm()) {
            event.preventDefault(); // Prevent form submission if validation fails
        }
    };

    // Event listener for the "Register Now" link
    document.querySelector('.signup-link a').addEventListener('click', function (event) {
        event.preventDefault(); // Prevent default link behavior
        openRegisterPopup();
    });
});

document.addEventListener('DOMContentLoaded', function () {
    // Attach validation to form submissions
    document.querySelector('#changePasswordPopup form').onsubmit = function (event) {
        if (!validateChangePasswordForm()) {
            event.preventDefault(); // Prevent form submission if validation fails
        } else {
            alert('Password changed successfully!');
            closeChangePasswordPopup();
        }
    };
});