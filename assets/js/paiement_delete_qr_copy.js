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
    notification.textContent = 'copied to clipboard!';

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
