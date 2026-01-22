<?php
/**
 * Fichier de configuration centralisé et sécurisé
 * Ce fichier charge les variables d'environnement et configure la sécurité
 */

// Chargement des variables d'environnement depuis .env
function loadEnv($path) {
    if (!file_exists($path)) {
        die("Erreur: Le fichier .env est manquant. Veuillez copier .env.example vers .env et configurer vos paramètres.");
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
        }
    }
}

// Charger le fichier .env
loadEnv(__DIR__ . '/../.env');

// Configuration de la base de données
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'resto');

// Configuration de sécurité
define('APP_ENV', $_ENV['APP_ENV'] ?? 'production');
define('SESSION_NAME', $_ENV['SESSION_NAME'] ?? 'RESTO_SESSION');
define('SESSION_LIFETIME', $_ENV['SESSION_LIFETIME'] ?? 3600);

/**
 * Configuration sécurisée de la session PHP
 */
function configureSecureSession() {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    
    session_name(SESSION_NAME);
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Régénération de l'ID de session pour prévenir le session fixation
    if (!isset($_SESSION['initiated'])) {
        session_regenerate_id(true);
        $_SESSION['initiated'] = true;
        $_SESSION['created_at'] = time();
    }
    
    // Vérifier l'expiration de la session
    if (isset($_SESSION['created_at']) && (time() - $_SESSION['created_at'] > SESSION_LIFETIME)) {
        session_unset();
        session_destroy();
        session_start();
    }
}

/**
 * Connexion sécurisée à la base de données
 */
function getDbConnection() {
    static $conn = null;
    
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            error_log("Erreur de connexion DB: " . $conn->connect_error);
            
            if (APP_ENV === 'development') {
                die("Erreur de connexion à la base de données: " . $conn->connect_error);
            } else {
                die("Erreur de connexion à la base de données. Veuillez réessayer plus tard.");
            }
        }
        
        // Définir le charset pour éviter les injections
        $conn->set_charset("utf8mb4");
    }
    
    return $conn;
}

/**
 * Génération de token CSRF
 */
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérification du token CSRF
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Headers de sécurité HTTP
 */
function setSecurityHeaders() {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    if (isset($_SERVER['HTTPS'])) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

/**
 * Validation d'email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validation de date (format YYYY-MM-DD)
 */
function validateDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

/**
 * Validation de l'heure (format HH:MM)
 */
function validateTime($time) {
    return preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time);
}

/**
 * Sanitization des entrées
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Réponse JSON sécurisée
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Vérification de l'authentification
 */
function requireAuth() {
    if (!isset($_SESSION['user_id'])) {
        jsonResponse(['success' => false, 'error' => 'Non authentifié'], 401);
    }
}

/**
 * Vérification du rôle admin
 */
function requireAdmin() {
    requireAuth();
    if ($_SESSION['role'] !== 'admin') {
        jsonResponse(['success' => false, 'error' => 'Accès refusé'], 403);
    }
}

// Initialiser les headers de sécurité
setSecurityHeaders();
?>
