/**
 * Gestion centralisée des tokens CSRF
 * Ce fichier gère la récupération et l'utilisation des tokens CSRF
 */

// Stockage du token CSRF en mémoire
let csrfToken = null;

/**
 * Récupère le token CSRF depuis le backend
 */
async function getCsrfToken() {
    if (csrfToken) {
        return csrfToken;
    }

    try {
        const response = await fetch('backend/get_csrf_token.php', {
            method: 'GET',
            credentials: 'same-origin'
        });

        if (!response.ok) {
            throw new Error('Impossible de récupérer le token CSRF');
        }

        const data = await response.json();
        if (data.csrf_token) {
            csrfToken = data.csrf_token;
            return csrfToken;
        } else {
            throw new Error('Token CSRF manquant dans la réponse');
        }
    } catch (error) {
        console.error('Erreur lors de la récupération du token CSRF:', error);
        return null;
    }
}

/**
 * Ajoute le token CSRF à un FormData
 * @param {FormData} formData - L'objet FormData à enrichir
 */
async function addCsrfToken(formData) {
    const token = await getCsrfToken();
    if (token) {
        formData.append('csrf_token', token);
    }
    return formData;
}

/**
 * Réinitialise le token CSRF (après connexion/déconnexion)
 */
function resetCsrfToken() {
    csrfToken = null;
}

/**
 * Initialise le token CSRF au chargement de la page
 */
document.addEventListener('DOMContentLoaded', async () => {
    await getCsrfToken();
});
