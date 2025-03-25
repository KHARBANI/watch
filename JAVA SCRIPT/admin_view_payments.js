function viewPayment(id) {
    // Fetch customer details (this is a placeholder, implement AJAX call if needed)
    const paymentData = {
        1: { name: "Jane Smith", amount: "10000", status: "Paid", date: "2024-01-01" },
        2: { name: "Jenny Smith", amount: "20000", status: "Paid", date: "2024-02-04" },
    };

    const payment = paymentData[id];
    if (payment) {
        document.getElementById("modalContent").innerHTML = `
            <h3>Payment Information</h3>
            <p><strong>Name:</strong> ${payment.name}</p>
            <p><strong>Amount:</strong> ${payment.amount}</p>
            <p><strong>Status:</strong> ${payment.status}</p>
            <p><strong>Date:</strong> ${payment.date}</p>
        `;
        document.getElementById("modal").style.display = "block";
    }
}

function closeInfoModal() {
    document.getElementById("modal").style.display = "none";
}