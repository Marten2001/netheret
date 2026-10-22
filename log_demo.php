<?php
$path = __DIR__ . '/logs/app.log';

$line = date('c') . ' | Ticket form opened' . PHP_EOL;
$handle = fopen($path,'a');

if (file_exists($path)) {
    echo '<pre>' . htmlspecialchars(file_get_contents($path)) . '</pre>';
}

if (isset($_GET['clear']) && file_exists($path)) {
    echo 'Log cleared.';
}