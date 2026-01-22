document.getElementById('signup-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    try {
        const formData = new FormData(e.target);

        // Ajouter le token CSRF
        await addCsrfToken(formData);

        const response = await fetch('backend/signup.php', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        });

        const data = await response.json();

        if (data.success) {
            // Réinitialiser le token CSRF après inscription
            resetCsrfToken();

            const formContainer = document.getElementById('sign-up-form');
            showSuccess(data.message || 'Inscription réussie', formContainer);

            // Redirection après un court délai
            setTimeout(() => {
                window.location.href = 'ReservationPage.html';
            }, 1500);
        } else {
            const formContainer = document.getElementById('sign-up-form');
            showError(data.error || 'Erreur lors de l\'inscription', formContainer);
        }
    } catch (error) {
        console.error('Erreur lors de l\'inscription:', error);
        const formContainer = document.getElementById('sign-up-form');
        showError('Une erreur est survenue. Veuillez réessayer.', formContainer);
    }
});
