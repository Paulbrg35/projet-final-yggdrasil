<?php
// Empêche l'accès direct
if (!defined('YGGDRASIL_CONFIG')) {
    http_response_code(403);
    die('Accès interdit');
}

// === CONFIGURATION DES SESSIONS ===
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params(3600);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// === CONFIGURATION DB ===
$config = [
    'host' => 'localhost',
    'dbname' => 'yggdrasil',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];

$dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];

try {
    $pdo = new PDO($dsn, $config['username'], $config['password'], $options);
    // ✅ Connexion réussie
} catch (PDOException $e) {
    error_log("Échec de connexion PDO : " . $e->getMessage());
    die("Erreur de base de données. Vérifiez que le serveur MySQL est lancé et que la base 'yggdrasil' existe.");
}

// Nettoyer
unset($config['password']);

// Fonction utilitaire
function getDatabase() {
    global $pdo;
    return $pdo;
}
?>