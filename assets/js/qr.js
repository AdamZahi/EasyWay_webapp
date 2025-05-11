function generateQRCode(payId) {
    fetch(`/paiement/qrcode/${payId}`)
        .then(response => {
            if (response.ok) {
                return response.blob();
            } else {
                throw new Error('QR code generation failed.');
            }
        })
        .then(blob => {
            const qrCodeUrl = URL.createObjectURL(blob);
            const qrImage = document.getElementById('qrImage');
            if (qrImage) {
                qrImage.src = qrCodeUrl;
            } else {
                alert("QR code generated successfully!");
            }
        })
        .catch(error => {
            alert("Error generating QR code: " + error.message);
        });
}
