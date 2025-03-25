// script.js

document.getElementById('payment-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent form from submitting normally

    // Simulate payment processing
    const paymentMessage = document.getElementById('payment-message');
    paymentMessage.textContent = 'Processing payment...';
    paymentMessage.style.backgroundColor = '#ffeb3b';
    paymentMessage.classList.remove('hidden');

    // Simulate a delay for payment processing
    setTimeout(function() {
        paymentMessage.textContent = 'Payment Successful!';
        paymentMessage.style.backgroundColor = '#28a745';
    }, 2000);

    // In a real-world scenario, you would send the payment details to your server
    // and handle the payment processing via a payment gateway like Stripe or PayPal.
});