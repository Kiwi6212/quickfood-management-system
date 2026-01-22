/**
 * Gestion de l'authentification côté client
 * Vérifie l'état de connexion et gère les redirections
 */

/**
 * Vérifie si l'utilisateur est connecté
 */
async function isAuthenticated() {
    try {
        const response = await fetch('backend/check_auth.php', {
            method: 'GET',
            credentials: 'same-origin'
        });

        if (!response.ok) {
            return false;
        }

        const data = await response.json();
        return data.authenticated === true;
    } catch (error) {
        console.error('Erreur lors de la vérification de l\'authentification:', error);
        return false;
    }
}

/**
 * Redirige vers la page de connexion si non authentifié
 */
async function requireAuth() {
    const authenticated = await isAuthenticated();
    if (!authenticated) {
        window.location.href = 'ComptePage.html';
        return false;
    }
    return true;
}

/**
 * Déconnecte l'utilisateur
 */
async function logout() {
    try {
        const response = await fetch('backend/logout.php', {
            method: 'POST',
            credentials: 'same-origin'
        });

        if (response.ok) {
            resetCsrfToken(); // Réinitialiser le token CSRF
            window.location.href = 'index.html';
        } else {
            console.error('Erreur lors de la déconnexion');
        }
    } catch (error) {
        console.error('Erreur lors de la déconnexion:', error);
    }
}

/**
 * Affiche un message d'erreur de manière sécurisée
 * @param {string} message - Le message à afficher
 * @param {HTMLElement} container - Le conteneur où afficher le message
 */
function showError(message, container = null) {
    // Échapper le HTML pour éviter les injections XSS
    const safeMessage = document.createTextNode(message);
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.style.cssText = 'color: #d32f2f; background: #ffebee; padding: 10px; margin: 10px 0; border-radius: 4px; border-left: 4px solid #d32f2f;';
    errorDiv.appendChild(safeMessage);

    if (container) {
        // Supprimer les anciens messages d'erreur
        const oldErrors = container.querySelectorAll('.error-message');
        oldErrors.forEach(err => err.remove());
        container.insertBefore(errorDiv, container.firstChild);
    } else {
        document.body.insertBefore(errorDiv, document.body.firstChild);
    }

    // Supprimer le message après 5 secondes
    setTimeout(() => errorDiv.remove(), 5000);
}

/**
 * Affiche un message de succès de manière sécurisée
 * @param {string} message - Le message à afficher
 * @param {HTMLElement} container - Le conteneur où afficher le message
 */
function showSuccess(message, container = null) {
    const safeMessage = document.createTextNode(message);
    const successDiv = document.createElement('div');
    successDiv.className = 'success-message';
    successDiv.style.cssText = 'color: #2e7d32; background: #e8f5e9; padding: 10px; margin: 10px 0; border-radius: 4px; border-left: 4px solid #2e7d32;';
    successDiv.appendChild(safeMessage);

    if (container) {
        const oldMessages = container.querySelectorAll('.success-message');
        oldMessages.forEach(msg => msg.remove());
        container.insertBefore(successDiv, container.firstChild);
    } else {
        document.body.insertBefore(successDiv, document.body.firstChild);
    }

    setTimeout(() => successDiv.remove(), 5000);
}

/**
 * Sanitize HTML pour éviter les injections XSS
 * @param {string} str - La chaîne à nettoyer
 * @returns {string} - La chaîne nettoyée
 */
function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}
