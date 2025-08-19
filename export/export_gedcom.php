<?php
session_start();
require_once 'config.php';

// 🔐 Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Récupérer les données de l'utilisateur (exemple simple)
try {
    $stmt = $pdo->prepare("SELECT firstname, lastname FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    // Générer le contenu GEDCOM
    $gedcom = "0 HEAD\n";
    $gedcom .= "1 SOUR Yggdrasil\n";
    $gedcom .= "2 NAME Yggdrasil Web Platform\n";
    $gedcom .= "1 DATE " . date('d M Y') . "\n";
    $gedcom .= "0 @U1@ INDI\n";
    $gedcom .= "1 NAME " . $user['firstname'] . " /" . $user['lastname'] . "/\n";
    $gedcom .= "1 SEX M\n"; // À adapter selon vos données
    $gedcom .= "1 BIRT\n";
    $gedcom .= "2 DATE 1 JAN 1990\n";
    $gedcom .= "1 FAMS @F1@\n";
    $gedcom .= "0 @F1@ FAM\n";
    $gedcom .= "1 HUSB @U1@\n";
    $gedcom .= "1 CHIL @U2@\n";
    $gedcom .= "0 TRLR\n";

    // En-têtes pour forcer le téléchargement
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="yggrasil_family_tree.ged"');
    header('Content-Length: ' . strlen($gedcom));
    echo $gedcom;
    exit();

} catch (Exception $e) {
    die("Erreur lors de l'export GEDCOM.");
}
?>