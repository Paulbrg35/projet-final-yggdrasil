<?php
// Empêche l'accès direct
if (!defined('YGGDRASIL_CONFIG')) {
    http_response_code(403);
    die('Accès interdit');
}

// === CONFIGURATION DES SESSIONS ===
$session_lifetime = 3600; // 1h

if (session_status() === PHP_SESSION_NONE) {
    // Définir les paramètres AVANT de démarrer la session
    ini_set('session.gc_maxlifetime', $session_lifetime);
    session_set_cookie_params([
        'lifetime' => $session_lifetime,
        'path'     => '/',
        'secure'   => false, // mets true si HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();
}

// === CONFIGURATION DE LA BASE DE DONNÉES ===
$config = [
    'host' => 'localhost',        // Généralement localhost en local
    'dbname' => 'yggdrasil',      // Nom de votre base de données
    'username' => 'root',         // Par défaut sur WAMP/XAMPP
    'password' => '',             // Souvent vide en local
    'charset' => 'utf8mb4',
    'port' => 3306
];

// Création du DSN (Data Source Name)
$dsn = sprintf(
    "mysql:host=%s;port=%d;dbname=%s;charset=%s",
    $config['host'],
    $config['port'],
    $config['dbname'],
    $config['charset']
);

// Options PDO recommandées
$pdoOptions = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$config['charset']} COLLATE {$config['charset']}_unicode_ci"
];

// Connexion à la base de données
try {
    $pdo = new PDO($dsn, $config['username'], $config['password'], $pdoOptions);
} catch (PDOException $e) {
    error_log("Erreur de connexion à la base de données : " . $e->getMessage());
    die("Impossible de se connecter à la base de données. Vérifiez que MySQL est lancé et que la base 'yggdrasil' existe.");
}

// Nettoyer les données sensibles
unset($config['password']);

// === FONCTION POUR ACCÉDER À LA BASE DE DONNÉES ===
function getDatabase() {
    global $pdo;
    return $pdo;
}

// === FONCTION DE JOURNALISATION (optionnelle) ===
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

// === FIN DU FICHIER ===
?>