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

// Function to close the change password popup
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
    const email = document.getElementById('loginEmailUnique').value; // Updated ID
    const password = document.getElementById('loginPasswordUnique').value; // Updated ID
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!email || !password) {
        alert("Please fill in all fields.");
        return false;
    }
    if (!emailPattern.test(email)) {
        alert("Please enter a valid email address.");
        return false;
    }
    return true;
}

function validateForgotPasswordForm(event){
    const email = document.getElementById('forgotPasswordEmail').value;
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if(!email){
        alert("Please enter your email address.");
        return false;
    }
    if(!emailPattern.test(email)){
        alert("Please enter a valid email address.");
        return false;
    }
    alert("Instructions have been sent to your email.");
    event.preventDefault(); // Prevent the form from submitting

    return false; // Prevent form submission
}

function validateRegistrationForm() {
    const name = document.getElementById('nameUnique').value; // Updated ID
    const email = document.getElementById('registerEmailUnique').value; // Updated ID
    const phone = document.getElementById('phoneUnique').value; // Updated ID
    const password = document.getElementById('registerPasswordUnique').value; // Updated ID
    const confirmPassword = document.getElementById('registerConfirmPasswordUnique').value; // Updated ID
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const namePattern = /^[A-Za-z\s]+$/; // Name validation pattern
    const passwordPattern = /^(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/; // Password validation pattern

    if (!name || !email || !phone || !password || !confirmPassword) {
        alert("Please fill in all fields.");
        return false;
    }
    if(name.length < 3){
        alert("Name must be at least 3 characters long.");
        return false;
    }
    else if(!namePattern.test(name)) {
        alert("Name can only contain letters and spaces.");
        return false;
    }
    if (!emailPattern.test(email)) {
        alert("Please enter a valid email address.");
        return false;
    }
    if (!phone.match(/^\d{10}$/)) {
        alert("Please enter a valid 10-digit phone number.");
        return false;
    }
    
    if (!passwordPattern.test(password)) {
        alert("Password must be at least 8 characters long and include at least one number and one special character.");
        return false;
    }
    if (password !== confirmPassword) {
        alert("Passwords do not match.");
        return false;
    }
    return true;
}

// Ensure event listeners are set up after DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Attach validation to form submissions
    document.querySelector('#loginPopup form').onsubmit = function(event) {
        if (!validateLoginForm()) {
            event.preventDefault(); // Prevent form submission if validation fails
        }
    };

    document.querySelector('#forgotPasswordPopup form').onsubmit = function(event) {
        if (!validateForgotPasswordForm(event)) {
            event.preventDefault(); // Prevent form submission if validation fails
        }
    };

    document.querySelector('#registerPopup form').onsubmit = function(event) {
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
