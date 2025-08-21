<?php
session_start();

// === CONFIGURATION ===
require_once 'config.php'; // PDO

/** @var PDO $pdo */
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");

// Initialisation
$errors = [];
$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';

// === ANTI-BRUTE FORCE ===
$max_attempts = 5;
$lockout_time = 300; // 5 minutes

if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= $max_attempts) {
    $last_attempt = $_SESSION['last_attempt_time'] ?? time();
    if (time() - $last_attempt < $lockout_time) {
        $errors[] = "Trop de tentatives. Veuillez réessayer dans " . ceil(($lockout_time - (time() - $last_attempt)) / 60) . " minutes.";
    } else {
        $_SESSION['login_attempts'] = 0;
    }
}

// === VALIDATION DES CHAMPS ===
if (!$email) {
    $errors[] = "Veuillez entrer une adresse email valide.";
}
if (empty($password)) {
    $errors[] = "Veuillez entrer un mot de passe.";
}

// === SI PAS D'ERREURS, ON VÉRIFIE L'UTILISATEUR ===
if (empty($errors)) {
    try {
        $stmt = $pdo->prepare("SELECT id, firstname, lastname, email, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // ✅ Connexion réussie
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname'] = $user['lastname'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['logged_in'] = true;

            // 🔐 Sécurité
            session_regenerate_id(true);

            // 🚀 Redirection
            $firstname = urlencode($user['firstname']);
            header("Location: dashboard.html?firstname=$firstname");
            exit();
        } else {
            $errors[] = "Email ou mot de passe incorrect.";
        }
    } catch (Exception $e) {
        error_log("Erreur login_process.php : " . $e->getMessage());
        $errors[] = "Une erreur est survenue. Veuillez réessayer.";
    }
}

// === GESTION DES ERREURS ===
if (!empty($errors)) {
    $_SESSION['login_errors'] = $errors;
    $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
    $_SESSION['last_attempt_time'] = time();

    header("Location: login.php");
    exit();
}
?>