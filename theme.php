<?php
$allowed = ['light', 'dark'];

if (isset($_GET['theme']) && in_array($_GET['theme'], $allowed, true)) {

    $theme = $_GET['theme'];
} else {
    $theme = $_COOKIE['theme'] ?? 'light';
    if (!in_array($theme, $allowed, true)) { $theme = 'light'; }
}