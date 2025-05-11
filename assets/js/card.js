document.addEventListener('DOMContentLoaded', () => {
    const hiddenInput = document.querySelector('input[name$="[nb]"]');

    if (!hiddenInput) {
        console.error('Hidden input for nb not found');
        return;
    }

    document.querySelectorAll('.seat-card').forEach(card => {
        card.addEventListener('click', function () {
            // Update card selection
            document.querySelectorAll('.seat-card').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');

            // Set the selected value in the hidden input
            hiddenInput.value = card.getAttribute('data-value');
        });
    });
});
