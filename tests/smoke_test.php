<?php
if (php_sapi_name() !== 'cli') {
    header('Contenr-Type: text/plain');
}

$base = 'http://127.0.0.1/netheret/';

$checks = [
    ['login page loads', 'login.php', [], 200],
    ['ticket form loads', 'ticket_form.php', [], 200],
    ['tickets page needs login', 'tickets.php', [], 302],
    ['API refuses a missing key', 'api/tickets.php', [], 401],
    ['API accepts the right key', 'api/tickets.php', ['X-API-Key: demo-key-123'], 200],
    ['missing page gives 404', 'no_such_page.php', [], 404],
    ['health check', 'health.php', [], 200],
];

$failures = 0;
foreach ($checks as [$name, $path, $headers, $expected]){
    $ch = curl_init($base . $path);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_HTTPHEADER => $headers,
    ]);
    curl_exec($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

    if ($status === $expected) {
        echo "PASS - $name\n";
    }else{
        echo "FAIL - $name (expected $expected, got $status)\n";
        $failures++;
    }
}

echo $failures === 0 ? "Smoke test: all passed\n " : "Smoke test: $failures failed\n";
exit($failures > 0 ? 1 : 0);