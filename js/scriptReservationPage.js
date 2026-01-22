// Vérifier l'authentification au chargement de la page
document.addEventListener('DOMContentLoaded', async () => {
    await requireAuth();
});

document.getElementById('reservation-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    // Récupération des valeurs saisies
    const date = document.getElementById('date').value;
    const time = document.getElementById('time').value;
    const table = document.getElementById('table').value;
    const menu = document.getElementById('menu').value;

    // Validation côté client
    if (!date || !time || !table || !menu) {
        showError("Veuillez remplir tous les champs avant de continuer.");
        return;
    }

    try {
        // Envoi des données au serveur
        const formData = new FormData();
        formData.append('date', date);
        formData.append('time', time);
        formData.append('table', table);
        formData.append('menu', menu);

        // Ajouter le token CSRF
        await addCsrfToken(formData);

        const response = await fetch('backend/reservation.php', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        });

        const data = await response.json();

        if (data.success) {
            showSuccess(data.message || 'Réservation réussie !');

            // Redirection après un court délai
            setTimeout(() => {
                window.location.href = 'SuccesPage.html';
            }, 1500);
        } else {
            showError(data.error || 'Erreur lors de la réservation');
        }
    } catch (error) {
        console.error('Erreur lors de la réservation:', error);
        showError('Une erreur est survenue. Veuillez réessayer plus tard.');
    }
});
