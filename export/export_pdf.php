<?php
session_start();
require_once 'config.php';
require_once 'tcpdf/tcpdf.php'; // Chemin vers TCPDF

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Récupérer les données
$stmt = $pdo->prepare("SELECT firstname, lastname, email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Créer un PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetCreator('Yggdrasil');
$pdf->SetAuthor($user['firstname'] . ' ' . $user['lastname']);
$pdf->SetTitle('Arbre Généalogique - Yggdrasil');
$pdf->SetSubject('Généalogie familiale');

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();

// HTML du contenu
$html = '
<h1 style="color:#2E5D42; text-align:center;">Arbre Généalogique</h1>
<h2 style="color:#D4AF37;">Propriétaire : ' . htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) . '</h2>
<p><strong>Email :</strong> ' . htmlspecialchars($user['email']) . '</p>
<p><strong>Date d\'export :</strong> ' . date('d/m/Y à H:i') . '</p>
<hr>
<h3>Exemple de membre</h3>
<ul>
    <li><strong>Nom :</strong> ' . htmlspecialchars($user['firstname']) . ' ' . htmlspecialchars($user['lastname']) . '</li>
    <li><strong>Naissance :</strong> 1er Janvier 1990</li>
    <li><strong>Relation :</strong> Chef de famille</li>
</ul>
<p style="font-style:italic; color:#666;">Ce document est généré par Yggdrasil - 2025</p>
';

$pdf->writeHTML($html, true, false, true, false, '');

// Télécharger le PDF
$pdf->Output('arbre_yggdrasil.pdf', 'D'); // 'D' = téléchargement
exit();