<?php
require __DIR__ . '/includes/db.php';
header('Content-Type: application/json');

$pdo->query('SELECT 1');
echo json_encode(['status' => 'ok', 'time' => date('c')]);