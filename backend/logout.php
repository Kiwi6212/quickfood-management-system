<?php
require_once 'config.php';

// Configuration de la session sécurisée
configureSecureSession();

// Vérifier que la requête est POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Méthode non autorisée'], 405);
}

// Détruire la session
session_unset();
session_destroy();

// Supprimer le cookie de session
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

jsonResponse(['success' => true, 'message' => 'Déconnexion réussie']);
?>
