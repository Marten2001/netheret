<?php
require __DIR__ . '/includes/db.php';
$allowed = ['open', 'in_progress', 'resolved'];
$id = (int) ($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';

//TODO 1
//TODO 2

header('Location: tickets.php');
exit;