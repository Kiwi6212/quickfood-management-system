<?php
require_once 'config.php';

// Configuration de la session sécurisée
configureSecureSession();

// Vérifier que l'utilisateur est admin
requireAdmin();

// Connexion à la base de données
$conn = getDbConnection();

// Récupération des réservations avec les informations des utilisateurs
$result = $conn->query("SELECT reservations.*, users.name, users.email 
                        FROM reservations 
                        JOIN users ON reservations.user_id = users.id 
                        ORDER BY reservations.reservation_date DESC, reservations.reservation_time DESC");

if (!$result) {
    error_log("Erreur de requête: " . $conn->error);
    die("Erreur lors de la récupération des réservations");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        .reservation { 
            border: 1px solid #ddd; 
            padding: 15px; 
            margin: 10px 0; 
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .reservation strong { color: #555; }
    </style>
</head>
<body>
    <h1>Tableau de bord Admin - Réservations</h1>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='reservation'>";
            echo "<p><strong>Client:</strong> " . htmlspecialchars($row['name']) . " (" . htmlspecialchars($row['email']) . ")</p>";
            echo "<p><strong>Date:</strong> " . htmlspecialchars($row['reservation_date']) . " à " . htmlspecialchars($row['reservation_time']) . "</p>";
            echo "<p><strong>Table:</strong> " . htmlspecialchars($row['table_number']) . "</p>";
            echo "<p><strong>Menu:</strong> " . htmlspecialchars($row['menu_choice']) . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>Aucune réservation pour le moment.</p>";
    }
    ?>
</body>
</html>
