<?php
/** Fetch a URL and decode the JSON. Returns ['status' => int, 'data' => ?array, 'error' => string]. */
function fetchJson(string $url): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 5,
        CURLOPT_CONNECTTIMEOUT => 3,
    ]);
    $body   = curl_exec($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);   // 0 if the connection failed
    $error  = curl_error($ch);

    $data = ($body !== false) ? json_decode($body, true) : null;
    return ['status' => $status, 'data' => is_array($data) ? $data : null, 'error' => $error];
}