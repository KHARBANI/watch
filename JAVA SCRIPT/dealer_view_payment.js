function viewPayment(id) {
    // Get the payment table body
    const paymentTableBody = document.getElementById("paymentTableBody");
    const rows = paymentTableBody.getElementsByTagName("tr");

    // Find the row with the matching ID
    for (let row of rows) {
        const paymentId = row.cells[0].innerText; // Payment ID is in the first cell
        if (paymentId == id) {
            const customerName = row.cells[1].innerText;
            const amount = row.cells[2].innerText;
            const status = row.cells[3].innerText;
            const date = row.cells[4].innerText;

            // Populate the modal with the payment details
            document.getElementById("modalContent").innerHTML = `
                <h3>Payment Information</h3>
                <p><strong>Payment ID:</strong> ${paymentId}</p>
                <p><strong>Customer Name:</strong> ${customerName}</p>
                <p><strong>Amount:</strong> ${amount}</p>
                <p><strong>Status:</strong> ${status}</p>
                <p><strong>Date:</strong> ${date}</p>
            `;
            document.getElementById("modal").style.display = "block";
            break;
        }
    }
}

function closeInfoModal() {
    document.getElementById("modal").style.display = "none";
    document.getElementById("modalContent").innerHTML = ""; // Clear modal content when closed
}
