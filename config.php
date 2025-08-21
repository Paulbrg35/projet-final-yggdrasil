<?php
// Empêche l'accès direct
if (!defined('YGGDRASIL_CONFIG')) {
    http_response_code(403);
    die('Accès interdit');
}
// Augmenter la durée de la session (en secondes)
ini_set('session.gc_maxlifetime', 3600); // 1 heure
session_set_cookie_params(3600);

// Démarrer la session si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Définir le mode (development, production, testing)
$environment = $_ENV['APP_ENV'] ?? 'development';

// Constantes de configuration
define('DEBUG_MODE', $environment === 'development');
define('RESET_COOLDOWN', 15);   // minutes
define('MAX_RESET_ATTEMPTS', 3);
define('TOKEN_EXPIRY', 1);       // heures
define('FROM_EMAIL', 'noreply@yggdrasil.bzh');
define('FROM_NAME', 'Yggdrasil');

// Configuration selon l'environnement
switch ($environment) {
    case 'production':
        $config = [
            'host' => $_ENV['DB_HOST'] ?? 'localhost',
            'dbname' => $_ENV['DB_NAME'] ?? 'yggdrasil_prod',
            'username' => $_ENV['DB_USER'] ?? 'root',
            'password' => $_ENV['DB_PASS'] ?? '',
            'port' => $_ENV['DB_PORT'] ?? 3306,
            'charset' => 'utf8mb4',
            'ssl' => filter_var($_ENV['DB_SSL'] ?? false, FILTER_VALIDATE_BOOLEAN)
        ];
        break;

    case 'testing':
        $config = [
            'host' => $_ENV['TEST_DB_HOST'] ?? 'localhost',
            'dbname' => $_ENV['TEST_DB_NAME'] ?? 'yggdrasil_test',
            'username' => $_ENV['TEST_DB_USER'] ?? 'root',
            'password' => $_ENV['TEST_DB_PASS'] ?? '',
            'port' => $_ENV['TEST_DB_PORT'] ?? 3306,
            'charset' => 'utf8mb4',
            'ssl' => false
        ];
        break;

    default: // development
        $config = [
            'host' => 'localhost',
            'dbname' => 'yggdrasil',
            'username' => 'root', // Pour WAMP/XAMPP
            'password' => '',     // Souvent vide en local
            'port' => 3306,
            'charset' => 'utf8mb4',
            'ssl' => false
        ];
}

// Validation des paramètres
if (empty($config['dbname'])) {
    error_log("ERREUR : Nom de base de données manquant");
    if (DEBUG_MODE) {
        die("Configuration de base de données invalide : nom manquant");
    } else {
        http_response_code(500);
        die("Erreur serveur");
    }
}

// Options PDO
$pdoOptions = [
    PDO::ATTR_ERRMODE               => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE    => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES      => false,
    PDO::MYSQL_ATTR_INIT_COMMAND    => "SET NAMES {$config['charset']} COLLATE {$config['charset']}_unicode_ci",
    PDO::MYSQL_ATTR_FOUND_ROWS      => true,
];

// DSN
$dsn = sprintf(
    "mysql:host=%s;port=%d;dbname=%s;charset=%s",
    $config['host'],
    $config['port'],
    $config['dbname'],
    $config['charset']
);

// Tentatives de connexion
$maxRetries = 3;
$pdo = null;

for ($i = 1; $i <= $maxRetries; $i++) {
    try {
        $pdo = new PDO($dsn, $config['username'], $config['password'], $pdoOptions);
        
        // Test simple
        $pdo->query("SELECT 1");
        break;

    } catch (PDOException $e) {
        error_log("Échec de connexion (tentative $i/$maxRetries) : " . $e->getMessage());
        
        if ($i === $maxRetries) {
            if (DEBUG_MODE) {
                die("Erreur de connexion à la base de données : " . $e->getMessage());
            } else {
                http_response_code(503);
                die("Service indisponible. Veuillez réessayer plus tard.");
            }
        }
        
        sleep(1); // Attendre avant nouvelle tentative
    }
}

// Fonction pour obtenir la connexion
function getDatabase() {
    global $pdo;
    return $pdo;
}

// Fonction de journalisation
function logSecurityEvent($event, $details = '') {
    $log = sprintf(
        "[%s] %s | IP: %s | User-Agent: %s | Details: %s\n",
        date('Y-m-d H:i:s'),
        $event,
        $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        $details
    );
    error_log($log, 3, __DIR__ . '/logs/security.log');
}

// Créer le dossier logs si nécessaire
if (!is_dir(__DIR__ . '/logs') && is_writable(__DIR__)) {
    mkdir(__DIR__ . '/logs', 0755, true);
}

// Nettoyer les variables sensibles
unset($config['password']);
?>