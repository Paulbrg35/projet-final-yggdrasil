<?php
session_start();
require_once 'config.php';
/** @var PDO $pdo */
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");

$data = json_decode(file_get_contents('php://input'), true);

$stmt = $pdo->prepare("DELETE FROM family_members WHERE id = ?");
$stmt->execute([$data['id']]);

echo json_encode(['success' => true]);
?>