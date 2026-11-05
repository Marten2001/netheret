<?php
require __DIR__ . '/includes/db.php';
$allowed = ['open', 'in_progress', 'resolved'];
$id = (int) ($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';

if ($id === 0 || !in_array($status, $allowed, true)) {
    http_response_code(400);
    exit;
}
$stmt = $pdo->prepare("UPDATE tickets SET status = ? WHERE id = ?");
$stmt->execute([$status, $id]);


header('Location: tickets.php');
exit;