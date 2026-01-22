<?php
require_once 'config.php';

// Configuration de la session sécurisée
configureSecureSession();

// Vérifier que l'utilisateur est admin
requireAdmin();

// Connexion à la base de données
$conn = getDbConnection();

// Vérification de la connexion
if ($conn->connect_error) {
    error_log("Erreur de connexion à la base de données : " . $conn->connect_error);
    die("Erreur de connexion à la base de données");
}

// Définition des noms complets des menus
$menuNames = [
    "Menu 1" => "Bœuf Bourguignon revisité",
    "Menu 2" => "Tarte Fine au Chèvre et Miel",
    "Menu 3" => "Soufflé au Chocolat Noir"
];

// Récupération des réservations
$result = $conn->query("SELECT reservations.*, users.name FROM reservations JOIN users ON reservations.user_id = users.id ORDER BY reservations.reservation_date DESC, reservations.reservation_time DESC");

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservations Clients</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        h2 { text-align: center; margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f1f1f1; }
        @media (max-width: 425px) {
            table, thead, tbody, th, td, tr {
                display: block; width: 100%;
            }
            th, td { padding: 10px; border: none; border-bottom: 1px solid #ddd; }
            td { display: flex; justify-content: space-between; }
            td::before { content: attr(data-label); flex: 1; font-weight: bold; margin-right: 10px; }
        }
    </style>
</head>
<body>
    <h2>Réservations Clients</h2>
    <table>
        <thead>
            <tr>
                <th>Nom du client</th>
                <th>Date</th>
                <th>Heure</th>
                <th>Table</th>
                <th>Menu</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Nettoyer et normaliser la clé
                    $menuKey = ucfirst(strtolower(trim($row['menu_choice'])));
                
                    // Débogage temporaire
                    echo "<!-- menu_choice nettoyé : [$menuKey] -->";
                
                    // Vérifier si la clé existe dans le tableau des menus
                    $menuChoice = isset($menuNames[$menuKey]) ? $menuNames[$menuKey] : "Menu inconnu";
                
                    echo '<tr>';
                    echo '<td data-label="Nom du client">' . htmlspecialchars($row['name']) . '</td>';
                    echo '<td data-label="Date">' . htmlspecialchars($row['reservation_date']) . '</td>';
                    echo '<td data-label="Heure">' . htmlspecialchars($row['reservation_time']) . '</td>';
                    echo '<td data-label="Table">' . htmlspecialchars($row['table_number']) . '</td>';
                    echo '<td data-label="Menu">' . htmlspecialchars($menuChoice) . '</td>';
                    echo '</tr>';
                }
                
            } else {
                echo '<tr><td colspan="5" style="text-align: center;">Aucune réservation trouvée.</td></tr>';
            }
            ?>
        </tbody>
    </table>
</body>
</html>
