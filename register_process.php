<?php
session_start();

// Configuration de la base de données
$host = 'localhost';
$dbname = 'yggdrasil'; // Remplacez par votre nom de base
$username = 'root';     // Remplacez par votre utilisateur
$password = '';         // Remplacez par votre mot de passe

// Tentative de connexion
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Initialisation des variables
$errors = [];
$success = false;

// Récupération et nettoyage des données
$firstname = trim($_POST['firstname'] ?? '');
$lastname = trim($_POST['lastname'] ?? '');
$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$country = $_POST['country'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm'] ?? '';

// Validation des champs
if (empty($firstname)) {
    $errors[] = "Le prénom est obligatoire.";
}

if (empty($lastname)) {
    $errors[] = "Le nom est obligatoire.";
}

if (!$email) {
    $errors[] = "L'adresse email n'est pas valide.";
}

if (empty($country)) {
    $errors[] = "Veuillez sélectionner un pays.";
}

// Validation du mot de passe
if (empty($password)) {
    $errors[] = "Le mot de passe est obligatoire.";
} else {
    if (strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Le mot de passe doit contenir au moins une majuscule.";
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Le mot de passe doit contenir au moins une minuscule.";
    }
    if (!preg_match('/\d/', $password)) {
        $errors[] = "Le mot de passe doit contenir au moins un chiffre.";
    }
    if (!preg_match('/[!@#$%^&*]/', $password)) {
        $errors[] = "Le mot de passe doit contenir au moins un caractère spécial (!@#$%^&*).";
    }
}

if ($password !== $confirm_password) {
    $errors[] = "Les mots de passe ne correspondent pas.";
}

// Si pas d'erreurs, on vérifie si l'email existe déjà
if (empty($errors)) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        $errors[] = "Un compte existe déjà avec cette adresse email.";
    } else {
        // Hash du mot de passe
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insertion dans la base
        $stmt = $pdo->prepare("INSERT INTO users (firstname, lastname, email, password, country, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        
        try {
            $stmt->execute([$firstname, $lastname, $email, $hashed_password, $country]);
            
            // Démarrer la session
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['firstname'] = $firstname;
            $_SESSION['lastname'] = $lastname;
            $_SESSION['email'] = $email;

            // Redirection vers le tableau de bord avec prénom
            header("Location: dashboard.html?firstname=" . urlencode($firstname));
            exit();
        } catch (Exception $e) {
            $errors[] = "Une erreur est survenue lors de l'inscription. Veuillez réessayer.";
        }
    }
}

// Si erreurs, retourner vers register.php avec les erreurs
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['form_data'] = [
        'firstname' => $firstname,
        'lastname' => $lastname,
        'email' => $email,
        'country' => $country
    ];
    header("Location: register.php");
    exit();
}
?>