<?php
session_start();
require_once 'config.php';

$user_id = $_GET['user_id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM family_members WHERE user_id = ?");
$stmt->execute([$user_id]);
echo json_encode($stmt->fetchAll());
?>