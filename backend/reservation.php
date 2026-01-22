<?php
require_once 'config.php';

// Configuration de la session sécurisée
configureSecureSession();

// Vérifier l'authentification
requireAuth();

// Vérifier que la requête est POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Méthode non autorisée'], 405);
}

// Vérifier le token CSRF
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    jsonResponse(['success' => false, 'error' => 'Token CSRF invalide'], 403);
}

// Validation des entrées
if (!isset($_POST['date']) || !isset($_POST['time']) || !isset($_POST['table']) || !isset($_POST['menu'])) {
    jsonResponse(['success' => false, 'error' => 'Tous les champs sont requis'], 400);
}

$user_id = $_SESSION['user_id'];
$date = $_POST['date'];
$time = $_POST['time'];
$table = intval($_POST['table']);
$menu = sanitizeInput($_POST['menu']);

// Validation de la date
if (!validateDate($date)) {
    jsonResponse(['success' => false, 'error' => 'Date invalide'], 400);
}

// Vérifier que la date est dans le futur
$reservationDate = new DateTime($date);
$today = new DateTime();
$today->setTime(0, 0, 0);

if ($reservationDate < $today) {
    jsonResponse(['success' => false, 'error' => 'La date de réservation doit être dans le futur'], 400);
}

// Validation de l'heure
if (!validateTime($time)) {
    jsonResponse(['success' => false, 'error' => 'Heure invalide'], 400);
}

// Validation du numéro de table
if ($table < 1 || $table > 20) {
    jsonResponse(['success' => false, 'error' => 'Numéro de table invalide'], 400);
}

// Validation du menu
$validMenus = ['Menu 1', 'Menu 2', 'Menu 3'];
if (!in_array($menu, $validMenus)) {
    jsonResponse(['success' => false, 'error' => 'Menu invalide'], 400);
}

// Connexion à la base de données
$conn = getDbConnection();

// Vérifier si la table est déjà réservée à cette date et heure
$checkQuery = $conn->prepare("SELECT id FROM reservations WHERE table_number = ? AND reservation_date = ? AND reservation_time = ?");
$checkQuery->bind_param("iss", $table, $date, $time);
$checkQuery->execute();
$checkResult = $checkQuery->get_result();

if ($checkResult->num_rows > 0) {
    jsonResponse(['success' => false, 'error' => 'Cette table est déjà réservée pour cette date et heure'], 409);
}

// Insertion de la réservation
$query = $conn->prepare("INSERT INTO reservations (user_id, reservation_date, reservation_time, table_number, menu_choice) VALUES (?, ?, ?, ?, ?)");
$query->bind_param("issis", $user_id, $date, $time, $table, $menu);

if ($query->execute()) {
    jsonResponse([
        'success' => true,
        'message' => 'Réservation effectuée avec succès',
        'reservation_id' => $conn->insert_id
    ]);
} else {
    error_log("Erreur de réservation: " . $conn->error);
    jsonResponse(['success' => false, 'error' => 'Erreur lors de la réservation'], 500);
}
?>
