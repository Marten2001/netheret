<?php

$url = 'http://127.0.0.1/netheret/health.php';
$total = 12;
$ok = 0; // Fixed variable name from $on to $ok

// Initialize cURL once outside the loop to reuse the connection
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 2, // Fail fast if connection hangs
    CURLOPT_TIMEOUT        => 3,
]);

for ($i = 1; $i <= $total; $i++) {
    curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

    if ($status === 200) {
        $ok++;
    }

    echo date('H:i:s') . " status $status\n";
    
    // Flush output buffer so you see logs in real-time
    if (ob_get_level() > 0) {
        ob_flush();
    }
    flush();

    // Only sleep if there are more iterations remaining
    if ($i < $total) {
        sleep(5);
    }
}

curl_close($ch);

$percentage = round(($ok / $total) * 100, 1);
echo "\nAvailability: $ok / $total = {$percentage}%\n";