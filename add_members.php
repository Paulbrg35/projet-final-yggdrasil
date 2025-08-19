<?php
session_start();
require_once 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

$stmt = $pdo->prepare("INSERT INTO family_members (user_id, name, surname, birth_date, death_date, gender, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([
    $data['user_id'],
    $data['name'],
    $data['surname'],
    $data['birth_date'] ?? null,
    $data['death_date'] ?? null,
    $data['gender'],
    $data['notes'] ?? ''
]);

echo json_encode(['success' => true]);
?>