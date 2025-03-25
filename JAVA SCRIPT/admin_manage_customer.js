function viewCustomer(id) {
    // Fetch customer details (this is a placeholder, implement AJAX call if needed)
    const customerData = {
        1: { name: "John Doe", email: "johndoe@gmail.com", phone: "9862457891", status: "Active" },
        2: { name: "Jane Smith", email: "janesmith@gmail.com", phone: "7253649658", status: "Active" },
        3: { name: "Jenny Smith", email: "jennysmith@gmail.com", phone: "9253649898", status: "Active" }
    };
    
    const customer = customerData[id];
    if (customer) {
        document.getElementById("modalContent").innerHTML = `
            <h3>Customer Information</h3>
            <p><strong>Name:</strong> ${customer.name}</p>
            <p><strong>Email:</strong> ${customer.email}</p>
            <p><strong>Phone:</strong> ${customer.phone}</p>
            <p><strong>Status:</strong> ${customer.status}</p>
        `;
        document.getElementById("modal").style.display = "block";
    }
}

function closeModal() {
    document.getElementById("modal").style.display = "none";
}

function deactivateUser(id) {
    const userRow = document.querySelector(`#userTableBody tr:nth-child(${id})`);
    const statusCell = userRow.children[4]; // Account Status cell
    const actionButton = userRow.querySelector('.delete-btn');

    if (statusCell.textContent === "Active") {
        statusCell.textContent = "Inactive";
        actionButton.textContent = "Activate";
    } else {
        statusCell.textContent = "Active";
        actionButton.textContent = "Deactivate";
    }
}
