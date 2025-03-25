function openEditModal(dealerId) {
    // ...existing code...
}

function closeEditModal() {
    // ...existing code...
}

function openViewModal(dealerId) {
    // Fetch dealer data using AJAX
    fetch(`get_dealer.php?dealer_id=${dealerId}`)
        .then(response => response.json())
        .then(data => {
            // Populate the modal with dealer details
            const details = `
                <p><strong>ID:</strong> ${data.Dealer_ID}</p>
                <p><strong>Name:</strong> ${data.Dealer_Name}</p>
                <p><strong>Email:</strong> ${data.Email}</p>
                <p><strong>Phone:</strong> ${data.Phone}</p>
                <p><strong>State:</strong> ${data.State_Name}</p>
                <p><strong>City:</strong> ${data.City_Name}</p>
                <p><strong>Street:</strong> ${data.Street_Address}</p>
                <p><strong>Postal Code:</strong> ${data.Postal_Code}</p>
                <p><strong>Brand:</strong> ${data.Brand_ID}</p>
                <p><strong>GST Number:</strong> ${data.GST_Number}</p>
                <p><strong>PAN Number:</strong> ${data.PAN_Number}</p>
                <p><strong>Account Status:</strong> ${data.Account_Status}</p>
            `;
            document.getElementById('viewDealerDetails').innerHTML = details;
            
            // Show the view modal
            document.getElementById('viewUserModal').style.display = 'block';
        })
        .catch(error => console.error('Error fetching dealer data:', error));
}

function closeViewModal() {
    document.getElementById('viewUserModal').style.display = 'none';
}