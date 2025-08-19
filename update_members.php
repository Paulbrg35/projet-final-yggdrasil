<?php
session_start();
require_once 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

$stmt = $pdo->prepare("UPDATE family_members SET name = ?, surname = ?, birth_date = ?, death_date = ?, gender = ?, notes = ? WHERE id = ?");
$stmt->execute([
    $data['name'],
    $data['surname'],
    $data['birth_date'] ?? null,
    $data['death_date'] ?? null,
    $data['gender'],
    $data['notes'] ?? '',
    $data['id']
]);

echo json_encode(['success' => true]);
?>