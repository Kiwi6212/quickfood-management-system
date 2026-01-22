<?php
require_once 'config.php';

// Configuration de la session sécurisée
configureSecureSession();

// Générer ou récupérer le token CSRF
$csrfToken = generateCsrfToken();

// Retourner le token au format JSON
jsonResponse([
    'success' => true,
    'csrf_token' => $csrfToken
]);
?>
