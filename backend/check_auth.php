<?php
require_once 'config.php';

// Configuration de la session sécurisée
configureSecureSession();

// Vérifier si l'utilisateur est authentifié
$authenticated = isset($_SESSION['user_id']);

// Retourner l'état d'authentification
jsonResponse([
    'authenticated' => $authenticated,
    'role' => $authenticated ? $_SESSION['role'] : null
]);
?>
