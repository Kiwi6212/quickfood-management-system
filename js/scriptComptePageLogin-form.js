document.getElementById('login-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    try {
        const formData = new FormData(e.target);

        // Ajouter le token CSRF
        await addCsrfToken(formData);

        const response = await fetch('backend/login.php', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        });

        const data = await response.json();

        if (data.success) {
            // Réinitialiser le token CSRF après connexion
            resetCsrfToken();

            // Redirection selon le rôle
            if (data.role === 'admin') {
                window.location.href = 'backend/ReservationClientPage.php';
            } else {
                window.location.href = 'ReservationPage.html';
            }
        } else {
            // Afficher l'erreur de manière sécurisée
            const formContainer = document.getElementById('sign-in-form');
            showError(data.error || 'Erreur de connexion', formContainer);
        }
    } catch (error) {
        console.error('Erreur lors de la connexion:', error);
        const formContainer = document.getElementById('sign-in-form');
        showError('Une erreur est survenue. Veuillez réessayer.', formContainer);
    }
});
