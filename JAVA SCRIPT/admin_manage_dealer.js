function openModal() {
    document.getElementById('addUserModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('addUserModal').style.display = 'none';
}

function openEditModal(dealerId) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'fetch_dealer.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var dealer = JSON.parse(xhr.responseText);
            document.getElementById('editDealerId').value = dealer.Dealer_ID;
            document.getElementById('editDealerName').value = dealer.Dealer_Name;
            document.getElementById('editDealerEmail').value = dealer.Email;
            document.getElementById('editPhone').value = dealer.Phone;
            document.getElementById('editState').value = dealer.State_ID;
            document.getElementById('editCity').value = dealer.City_ID;
            document.getElementById('editBrand').value = dealer.Brand_ID;
            document.getElementById('editAddress').value = dealer.Street_Address;
            document.getElementById('editPostalCode').value = dealer.Postal_Code;
            document.getElementById('editGST').value = dealer.GST_Number;
            document.getElementById('editPAN').value = dealer.PAN_Number;
            document.getElementById('editAccountStatus').value = dealer.Account_Status;
            document.getElementById('editUserModal').style.display = 'block';
        }
    };
    xhr.send('dealer_id=' + dealerId);
}

function closeEditModal() {
    document.getElementById('editUserModal').style.display = 'none';
}

document.getElementById('state').addEventListener('change', function() {
    var stateId = this.value;
    var citySelect = document.getElementById('city');
    citySelect.innerHTML = '<option value="">Select City</option><option value="new">+ Add New City</option>'; // Ensure "Add New City" option is always available

    if (stateId) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'fetch_cities.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var cities = JSON.parse(xhr.responseText);
                cities.forEach(function(city) {
                    var option = document.createElement('option');
                    option.value = city.City_ID;
                    option.textContent = city.City_Name;
                    citySelect.insertBefore(option, citySelect.querySelector('option[value="new"]'));
                });
            }
        };
        xhr.send('state_id=' + stateId);
    }

    var stateForm = document.getElementById('stateForm');
    if (stateId === 'new') {
        stateForm.style.display = 'block';
    } else {
        stateForm.style.display = 'none';
    }
});

document.getElementById('city').addEventListener('change', function() {
    var cityForm = document.getElementById('cityForm');
    if (this.value === 'new') {
        cityForm.style.display = 'block';
    } else {
        cityForm.style.display = 'none';
    }
});

document.getElementById('addStateBtn').addEventListener('click', function() {
    var newStateInput = document.getElementById('newStateInput');
    var newStateName = newStateInput.value.trim();
    var stateError = document.getElementById('stateError');
    if (newStateName) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'check_and_add_state.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    var stateSelect = document.getElementById('state');
                    var option = document.createElement('option');
                    option.value = response.state_id;
                    option.textContent = newStateName;
                    stateSelect.insertBefore(option, stateSelect.querySelector('option[value="new"]'));
                    stateSelect.value = response.state_id;
                    newStateInput.value = '';
                    document.getElementById('stateForm').style.display = 'none';
                    stateError.textContent = ''; // Clear any previous error message
                } else {
                    stateError.textContent = response.message; // Display the error message
                }
            }
        };
        xhr.send('state_name=' + encodeURIComponent(newStateName));
    }
});

document.getElementById('addCityBtn').addEventListener('click', function() {
    var newCityInput = document.getElementById('newCityInput');
    var newCityName = newCityInput.value.trim();
    var stateId = document.getElementById('state').value;
    var cityError = document.getElementById('cityError');
    if (newCityName && stateId) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'check_and_add_city.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                console.log('Response status: ' + xhr.status);
                console.log('Response text: ' + xhr.responseText);
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        var citySelect = document.getElementById('city');
                        var option = document.createElement('option');
                        option.value = response.city_id;
                        option.textContent = newCityName;
                        citySelect.insertBefore(option, citySelect.querySelector('option[value="new"]'));
                        citySelect.value = response.city_id;
                        newCityInput.value = '';
                        document.getElementById('cityForm').style.display = 'none';
                        cityError.textContent = ''; // Clear any previous error message
                    } else {
                        cityError.textContent = response.message; // Display the error message
                    }
                } catch (e) {
                    console.error('Failed to parse JSON response:', xhr.responseText);
                    cityError.textContent = 'Failed to add city. Please try again.';
                }
            }
        };
        xhr.send('city_name=' + encodeURIComponent(newCityName) + '&state_id=' + encodeURIComponent(stateId));
    } else {
        cityError.textContent = 'City name and state must be selected.';
    }
});

document.getElementById('brand').addEventListener('change', function() {
    var brandForm = document.getElementById('brandForm');
    if (this.value === 'new') {
        brandForm.style.display = 'block';
    } else {
        brandForm.style.display = 'none';
    }
});

document.getElementById('addBrandBtn').addEventListener('click', function() {
    var newBrandInput = document.getElementById('newBrandInput');
    var newBrandName = newBrandInput.value.trim();
    var brandError = document.getElementById('brandError');
    if (newBrandName) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'check_and_add_brand.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    var brandSelect = document.getElementById('brand');
                    var option = document.createElement('option');
                    option.value = response.brand_id;
                    option.textContent = newBrandName;
                    brandSelect.insertBefore(option, brandSelect.querySelector('option[value="new"]'));
                    brandSelect.value = response.brand_id;
                    newBrandInput.value = '';
                    document.getElementById('brandForm').style.display = 'none';
                    brandError.textContent = ''; // Clear any previous error message
                } else {
                    brandError.textContent = response.message; // Display the error message
                }
            }
        };
        xhr.send('brand_name=' + encodeURIComponent(newBrandName));
    }
});

document.getElementById('submitDealerForm').addEventListener('click', function() {
    var form = document.getElementById('addDealerForm');
    var formData = new FormData(form);
    var popupMessage = document.getElementById('popupMessage');

    // Enhanced validation
    var userName = formData.get('dealer_name');
    var userEmail = formData.get('email');
    var phone = formData.get('phone');
    var state = formData.get('state');
    var city = formData.get('city');
    var address = formData.get('street');
    var postalCode = formData.get('postal_code');
    var brand = formData.get('brand');
    var gst = formData.get('gst_number');
    var pan = formData.get('pan_number');

    // Clear previous error messages
    document.getElementById('dealer_nameError').textContent = '';
    document.getElementById('emailError').textContent = '';
    document.getElementById('phoneError').textContent = '';
    document.getElementById('stateError').textContent = '';
    document.getElementById('cityError').textContent = '';
    document.getElementById('streetError').textContent = '';
    document.getElementById('postal_codeError').textContent = '';
    document.getElementById('brandError').textContent = '';
    document.getElementById('gst_numberError').textContent = '';
    document.getElementById('pan_numberError').textContent = '';

    if (!userName || !userEmail || !phone || !state || !city || !address || !postalCode || !brand || !gst || !pan) {
        popupMessage.textContent = 'All fields are required.';
        popupMessage.className = 'error';
        popupMessage.style.display = 'block';
        setTimeout(function() {
            popupMessage.style.display = 'none';
        }, 3000);
        return;
    }

    // Validate Username
    if (!userName || /[^a-zA-Z\ ]/.test(userName)) {
        popupMessage.textContent = "Username is required and should not contain special characters and numbers.";
        popupMessage.className = 'error';
        popupMessage.style.display = 'block';
        setTimeout(function() {
            popupMessage.style.display = 'none';
        }, 3000);
        return;
    }

    // Email validation
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(userEmail)) {
        popupMessage.textContent = "Please enter a valid email address.";
        popupMessage.className = 'error';
        popupMessage.style.display = 'block';
        setTimeout(function() {
            popupMessage.style.display = 'none';
        }, 3000);
        return;
    }

    // Phone validation (example: must be 10 digits)
    const phonePattern = /^\d{10}$/;
    if (!phonePattern.test(phone)) {
        popupMessage.textContent = "Please enter a valid 10-digit phone number.";
        popupMessage.className = 'error';
        popupMessage.style.display = 'block';
        setTimeout(function() {
            popupMessage.style.display = 'none';
        }, 3000);
        return;
    }

    // Validate State
    if (!state || state === 'new') {
        popupMessage.textContent = "State is required.";
        popupMessage.className = 'error';
        popupMessage.style.display = 'block';
        setTimeout(function() {
            popupMessage.style.display = 'none';
        }, 3000);
        return;
    }

    // Validate City
    if (!city || city === 'new') {
        popupMessage.textContent = "City is required.";
        popupMessage.className = 'error';
        popupMessage.style.display = 'block';
        setTimeout(function() {
            popupMessage.style.display = 'none';
        }, 3000);
        return;
    }

    // Validate Address
    if (!address) {
        popupMessage.textContent = "Address is required.";
        popupMessage.className = 'error';
        popupMessage.style.display = 'block';
        setTimeout(function() {
            popupMessage.style.display = 'none';
        }, 3000);
        return;
    }

    // Validate Brand
    if (!brand || brand === 'new') {
        popupMessage.textContent = "Brand is required.";
        popupMessage.className = 'error';
        popupMessage.style.display = 'block';
        setTimeout(function() {
            popupMessage.style.display = 'none';
        }, 3000);
        return;
    }

    // Validate Postal Code
    if (!postalCode || postalCode.length !== 6) {
        popupMessage.textContent = "Postal code is required or should be 6 digits.";
        popupMessage.className = 'error';
        popupMessage.style.display = 'block';
        setTimeout(function() {
            popupMessage.style.display = 'none';
        }, 3000);
        return;
    }

    // Validate GST Number (example pattern)
    const gstPattern = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[0-9]{1}[Z]{1}[0-9A-Z]{1}$/;
    if (!gstPattern.test(gst)) {
        popupMessage.textContent = "Please enter a valid GST number.";
        popupMessage.className = 'error';
        popupMessage.style.display = 'block';
        setTimeout(function() {
            popupMessage.style.display = 'none';
        }, 3000);
        return;
    }

    // Validate PAN Number (example pattern)
    const panPattern = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
    if (!panPattern.test(pan)) {
        popupMessage.textContent = "Please enter a valid PAN number.";
        popupMessage.className = 'error';
        popupMessage.style.display = 'block';
        setTimeout(function() {
            popupMessage.style.display = 'none';
        }, 3000);
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'add_dealer.php', true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            console.log('Response status: ' + xhr.status);
            console.log('Response text: ' + xhr.responseText);
            try {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    popupMessage.textContent = 'Dealer added successfully';
                    popupMessage.className = 'success';
                    popupMessage.style.display = 'block';
                    setTimeout(function() {
                        popupMessage.style.display = 'none';
                        location.reload(); // Reload the page to update the dealer list
                    }, 3000);
                } else {
                    popupMessage.textContent = response.message;
                    popupMessage.className = 'error';
                    popupMessage.style.display = 'block';
                    setTimeout(function() {
                        popupMessage.style.display = 'none';
                    }, 3000);
                }
            } catch (e) {
                console.error('Failed to parse JSON response:', xhr.responseText);
                popupMessage.textContent = 'Failed to add dealer. Please try again.';
                popupMessage.className = 'error';
                popupMessage.style.display = 'block';
                setTimeout(function() {
                    popupMessage.style.display = 'none';
                }, 3000);
            }
        }
    };
    xhr.send(formData);
});

document.getElementById('submitEditDealerForm').addEventListener('click', function() {
    var form = document.getElementById('editDealerForm');
    var formData = new FormData(form);
    var popupMessage = document.getElementById('popupMessage');

    // Enhanced validation
    var userName = formData.get('dealer_name');
    var userEmail = formData.get('email');
    var phone = formData.get('phone');
    var state = formData.get('state');
    var city = formData.get('city');
    var address = formData.get('street');
    var postalCode = formData.get('postal_code');
    var brand = formData.get('brand');
    var gst = formData.get('gst_number');
    var pan = formData.get('pan_number');

    // Clear previous error messages
    document.getElementById('editDealerNameError').textContent = '';
    document.getElementById('editDealerEmailError').textContent = '';
    document.getElementById('editPhoneError').textContent = '';
    document.getElementById('editStateError').textContent = '';
    document.getElementById('editCityError').textContent = '';
    document.getElementById('editStreetError').textContent = '';
    document.getElementById('editPostalCodeError').textContent = '';
    document.getElementById('editBrandError').textContent = '';
    document.getElementById('editGSTError').textContent = '';
    document.getElementById('editPANError').textContent = '';

    if (!userName || !userEmail || !phone || !state || !city || !address || !postalCode || !brand || !gst || !pan) {
        popupMessage.textContent = 'All fields are required.';
        popupMessage.className = 'error';
        popupMessage.style.display = 'block';
        setTimeout(function() {
            popupMessage.style.display = 'none';
        }, 3000);
        return;
    }

    // Validate Username
    if (!userName || /[^a-zA-Z\ ]/.test(userName)) {
        popupMessage.textContent = "Username is required and should not contain special characters and numbers.";
        popupMessage.className = 'error';
        popupMessage.style.display = 'block';
        setTimeout(function() {
            popupMessage.style.display = 'none';
        }, 3000);
        return;
    }

    // Email validation
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(userEmail)) {
        popupMessage.textContent = "Please enter a valid email address.";
        popupMessage.className = 'error';
        popupMessage.style.display = 'block';
        setTimeout(function() {
            popupMessage.style.display = 'none';
        }, 3000);
        return;
    }

    // Phone validation (example: must be 10 digits)
    const phonePattern = /^\d{10}$/;
    if (!phonePattern.test(phone)) {
        popupMessage.textContent = "Please enter a valid 10-digit phone number.";
        popupMessage.className = 'error';
        popupMessage.style.display = 'block';
        setTimeout(function() {
            popupMessage.style.display = 'none';
        }, 3000);
        return;
    }

    // Validate State
    if (!state || state === 'new') {
        popupMessage.textContent = "State is required.";
        popupMessage.className = 'error';
        popupMessage.style.display = 'block';
        setTimeout(function() {
            popupMessage.style.display = 'none';
        }, 3000);
        return;
    }

    // Validate City
    if (!city || city === 'new') {
        popupMessage.textContent = "City is required.";
        popupMessage.className = 'error';
        popupMessage.style.display = 'block';
        setTimeout(function() {
            popupMessage.style.display = 'none';
        }, 3000);
        return;
    }

    // Validate Address
    if (!address) {
        popupMessage.textContent = "Address is required.";
        popupMessage.className = 'error';
        popupMessage.style.display = 'block';
        setTimeout(function() {
            popupMessage.style.display = 'none';
        }, 3000);
        return;
    }

    // Validate Brand
    if (!brand || brand === 'new') {
        popupMessage.textContent = "Brand is required.";
        popupMessage.className = 'error';
        popupMessage.style.display = 'block';
        setTimeout(function() {
            popupMessage.style.display = 'none';
        }, 3000);
        return;
    }

    // Validate Postal Code
    if (!postalCode || postalCode.length !== 6) {
        popupMessage.textContent = "Postal code is required or should be 6 digits.";
        popupMessage.className = 'error';
        popupMessage.style.display = 'block';
        setTimeout(function() {
            popupMessage.style.display = 'none';
        }, 3000);
        return;
    }

    // Validate GST Number (example pattern)
    const gstPattern = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[0-9]{1}[Z]{1}[0-9A-Z]{1}$/;
    if (!gstPattern.test(gst)) {
        popupMessage.textContent = "Please enter a valid GST number.";
        popupMessage.className = 'error';
        popupMessage.style.display = 'block';
        setTimeout(function() {
            popupMessage.style.display = 'none';
        }, 3000);
        return;
    }

    // Validate PAN Number (example pattern)
    const panPattern = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
    if (!panPattern.test(pan)) {
        popupMessage.textContent = "Please enter a valid PAN number.";
        popupMessage.className = 'error';
        popupMessage.style.display = 'block';
        setTimeout(function() {
            popupMessage.style.display = 'none';
        }, 3000);
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'edit_dealer.php', true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            console.log('Response status: ' + xhr.status);
            console.log('Response text: ' + xhr.responseText);
            try {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    popupMessage.textContent = 'Dealer updated successfully';
                    popupMessage.className = 'success';
                    popupMessage.style.display = 'block';
                    setTimeout(function() {
                        popupMessage.style.display = 'none';
                        location.reload(); // Reload the page to update the dealer list
                    }, 3000);
                } else {
                    popupMessage.textContent = response.message;
                    popupMessage.className = 'error';
                    popupMessage.style.display = 'block';
                    setTimeout(function() {
                        popupMessage.style.display = 'none';
                    }, 3000);
                }
            } catch (e) {
                console.error('Failed to parse JSON response:', xhr.responseText);
                popupMessage.textContent = 'Failed to update dealer. Please try again.';
                popupMessage.className = 'error';
                popupMessage.style.display = 'block';
                setTimeout(function() {
                    popupMessage.style.display = 'none';
                }, 3000);
            }
        }
    };
    xhr.send(formData);
});
