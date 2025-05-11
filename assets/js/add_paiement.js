document.addEventListener('DOMContentLoaded', function () {
    const stripe = Stripe(window.paiementData.stripePublicKey);
    const elements = stripe.elements();
    const card = elements.create("card");
    card.mount("#card-element");

    const form = document.getElementById("payment-form");
    form.addEventListener("submit", async (event) => {
        event.preventDefault();

        const { error, paymentIntent } = await stripe.confirmCardPayment(
            window.paiementData.clientSecret,
            {
                payment_method: {
                    card: card
                }
            }
        );

        const statusElement = document.getElementById('payment-status');
        if (error) {
            statusElement.innerHTML = `<div class="alert alert-danger animate__animated animate__shakeX">${error.message}</div>`;
        } else if (paymentIntent.status === 'succeeded') {
            statusElement.innerHTML = `<div class="alert alert-success animate__animated animate__bounceIn">Paiement réussi ! 🎉</div>`;
            // Sending the payment data to the server for confirmation
            fetch(window.paiementData.confirmUrl, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    payment_id: paymentIntent.id,
                    montant: window.paiementData.montant
                }),
            })
            .then(res => res.json());
        }
    });

    const title = document.querySelector('.floating-title');
    if (title) {
        title.classList.add('animate__animated', 'animate__fadeInDown');
    }
});
