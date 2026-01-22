<?php
require_once 'config.php';

// Configuration de la session sécurisée
configureSecureSession();

// Vérifier que la requête est POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Méthode non autorisée'], 405);
}

// Vérifier le token CSRF
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    jsonResponse(['success' => false, 'error' => 'Token CSRF invalide'], 403);
}

// Validation des entrées
if (!isset($_POST['email']) || !isset($_POST['password'])) {
    jsonResponse(['success' => false, 'error' => 'Email et mot de passe requis'], 400);
}

$email = $_POST['email'];
$password = $_POST['password'];

// Validation de l'email
if (!validateEmail($email)) {
    jsonResponse(['success' => false, 'error' => 'Email invalide'], 400);
}

// Validation du mot de passe (longueur minimale)
if (strlen($password) < 6) {
    jsonResponse(['success' => false, 'error' => 'Mot de passe trop court'], 400);
}

// Connexion à la base de données
$conn = getDbConnection();

// Requête préparée pour récupérer l'utilisateur
$query = $conn->prepare("SELECT id, role, password FROM users WHERE email = ?");
$query->bind_param("s", $email);
$query->execute();
$result = $query->get_result();

if ($user = $result->fetch_assoc()) {
    // Vérification du mot de passe avec password_verify
    if (password_verify($password, $user['password'])) {
        // Régénérer l'ID de session pour éviter le session fixation
        session_regenerate_id(true);
        
        // Stocker les informations de l'utilisateur dans la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $email;
        $_SESSION['last_activity'] = time();
        
        // Générer un nouveau token CSRF
        generateCsrfToken();
        
        jsonResponse([
            'success' => true,
            'role' => $user['role'],
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    } else {
        // Mot de passe incorrect
        jsonResponse(['success' => false, 'error' => 'Email ou mot de passe incorrect'], 401);
    }
} else {
    // Utilisateur non trouvé
    jsonResponse(['success' => false, 'error' => 'Email ou mot de passe incorrect'], 401);
}
?>
