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
if (!isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['password'])) {
    jsonResponse(['success' => false, 'error' => 'Tous les champs sont requis'], 400);
}

$name = sanitizeInput($_POST['name']);
$email = $_POST['email'];
$password = $_POST['password'];

// Validation du nom
if (strlen($name) < 2 || strlen($name) > 100) {
    jsonResponse(['success' => false, 'error' => 'Le nom doit contenir entre 2 et 100 caractères'], 400);
}

// Validation de l'email
if (!validateEmail($email)) {
    jsonResponse(['success' => false, 'error' => 'Email invalide'], 400);
}

// Validation du mot de passe
if (strlen($password) < 8) {
    jsonResponse(['success' => false, 'error' => 'Le mot de passe doit contenir au moins 8 caractères'], 400);
}

// Hachage du mot de passe
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Connexion à la base de données
$conn = getDbConnection();

// Vérifier si l'email existe déjà
$checkQuery = $conn->prepare("SELECT id FROM users WHERE email = ?");
$checkQuery->bind_param("s", $email);
$checkQuery->execute();
$checkResult = $checkQuery->get_result();

if ($checkResult->num_rows > 0) {
    jsonResponse(['success' => false, 'error' => 'Cet email est déjà utilisé'], 409);
}

// Insertion du nouvel utilisateur
$query = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'client')");
$query->bind_param("sss", $name, $email, $hashedPassword);

if ($query->execute()) {
    // Connexion automatique après inscription
    $_SESSION['user_id'] = $conn->insert_id;
    $_SESSION['role'] = 'client';
    $_SESSION['email'] = $email;
    $_SESSION['last_activity'] = time();
    
    // Générer un token CSRF
    generateCsrfToken();
    
    jsonResponse([
        'success' => true,
        'message' => 'Inscription réussie',
        'csrf_token' => $_SESSION['csrf_token']
    ]);
} else {
    error_log("Erreur d'inscription: " . $conn->error);
    jsonResponse(['success' => false, 'error' => 'Erreur lors de l\'inscription'], 500);
}
?>
