<?php
require __DIR__ . '/include/db.php';
$id = (int) ($_POST['id'] ?? 0);

//TODO 3
//TODO 4

header('Location: tickets.php');
exit;