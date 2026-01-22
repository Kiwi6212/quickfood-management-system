  // Fonction pour afficher le formulaire sélectionné
  function showForm(formId) {
    const forms = document.querySelectorAll('.auth-form'); // Tous les formulaires
    forms.forEach((form) => {
        form.style.display = 'none'; // Cacher tous les formulaires
    });
    document.getElementById(formId).style.display = 'block'; // Afficher le formulaire sélectionné
}

// Fonction pour revenir à l'accueil (cacher tous les formulaires)
function backToMain() {
    const forms = document.querySelectorAll('.auth-form');
    forms.forEach((form) => {
        form.style.display = 'none';
    });
}