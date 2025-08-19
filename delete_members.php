<?php
session_start();
require_once 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

$stmt = $pdo->prepare("DELETE FROM family_members WHERE id = ?");
$stmt->execute([$data['id']]);

echo json_encode(['success' => true]);
?>