// Function to reload page based on selected sort option
function handleSortChange() {
    var sortOption = document.getElementById("sort-options").value;
    var url = new URL(window.location.href);
    url.searchParams.set('sort', sortOption);  // Add or update the 'sort' query parameter
    window.location.href = url;  // Reload the page with the new sort parameter
}

// Function to copy the payment ID to clipboard and show the notification
function copyToClipboard(paymentId) {
    // Create a temporary input element to copy the text
    const tempInput = document.createElement('input');
    document.body.appendChild(tempInput);
    tempInput.value = paymentId; // Set the value to the payment ID
    tempInput.select(); // Select the input value
    document.execCommand('copy'); // Copy the text to the clipboard
    document.body.removeChild(tempInput); // Remove the temporary input

    // Create the notification element
    const notification = document.createElement('div');
    notification.classList.add('custom-notification');
    notification.textContent = 'Payment ID copied to clipboard!';

    // Append the notification to the body
    document.body.appendChild(notification);

    // Make the notification fade out after 3 seconds
    setTimeout(function() {
        notification.classList.add('fade-out');
    }, 3000);

    // Remove the notification from the DOM after it fades out
    setTimeout(function() {
        notification.remove();
    }, 3500);
}

// Placeholder function for deleting a payment (implement the actual logic)
function deletePaiement(paymentId) {
    if (confirm("Are you sure you want to delete this payment?")) {
        // Add the logic to delete the payment
        console.log('Deleting payment with ID: ' + paymentId);
    }
}

// Placeholder function for generating QR code (implement the actual logic)
function generateQRCode(paymentId) {
    // Add the logic to generate the QR code
    console.log('Generating QR code for payment with ID: ' + paymentId);
}