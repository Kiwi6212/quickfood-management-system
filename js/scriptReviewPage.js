let reviews = [
    'Bonne nourriture et service incroyable!',
    'Très propre et bien organisé.'
];

function handleReviewSubmit() {
    const newReviewInput = document.getElementById('new-review');
    const newReview = newReviewInput.value.trim();

    if (newReview) {
        // Validation de la longueur
        if (newReview.length < 5) {
            showError('Votre avis doit contenir au moins 5 caractères.');
            return;
        }

        if (newReview.length > 500) {
            showError('Votre avis ne peut pas dépasser 500 caractères.');
            return;
        }

        // Ajouter la nouvelle revue à la liste des avis
        reviews.push(newReview);
        newReviewInput.value = ''; // Réinitialiser le champ de saisie

        // Afficher le message de confirmation
        const successMessage = document.getElementById('success-message');
        successMessage.style.display = 'block';

        // Cacher le message après 3 secondes
        setTimeout(() => {
            successMessage.style.display = 'none';
        }, 3000);

        // Afficher les avis mis à jour
        updateReviewsList();
    } else {
        showError('Veuillez écrire un avis avant de soumettre.');
    }
}

function updateReviewsList() {
    const reviewsList = document.getElementById('reviews-list');
    reviewsList.innerHTML = '';

    reviews.forEach(review => {
        const li = document.createElement('li');
        // Utiliser textContent au lieu de innerHTML pour éviter les injections XSS
        li.textContent = review;
        reviewsList.appendChild(li);
    });
}
