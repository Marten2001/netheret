<?php
require __DIR__ . '/includes/http.php';
require __DIR__ . '/includes/functions.php';

$postcode = trim($_POST['postcode'] ?? '');
$result   = null;
$message  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isValidPostcode($postcode)) {
        $message = 'Enter a valid UK postcode.';
    }
    else{
        $response = fetchJson('https://api.postcodes.io/postcodes/' . rawurldecode($postcode));
        if ($response['status'] === 200) {
            $result = $response['data']['result'] ?? null;
        }
        elseif ($response['status'] === 404){
            $message = 'Postcode not found.';
        }
        else{
            error_log('Postcode API failure: status ' . $response['status'] . '' . $response['error']);
            $message = 'The postcode service is unavailable. Try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Postcode lookup</title><link rel="stylesheet" href="css/ux.css"></head>
<body>
<main>
  <h1>Check a site postcode</h1>
  <?php if ($message): ?><p class="error" role="alert"><?= htmlspecialchars($message) ?></p><?php endif; ?>

  <form method="post" action="postcode_lookup.php">
    <label for="postcode">Postcode</label>
    <input type="text" id="postcode" name="postcode" maxlength="10" value="<?= htmlspecialchars($postcode) ?>">
    <button type="submit">Look up</button>
  </form>

  <?php if ($result): ?>
    <h2>Result</h2>
    <ul>
      <li>Postcode: <?= htmlspecialchars($result['postcode'] ?? '') ?></li>
      <li>Area: <?= htmlspecialchars($result['admin_district'] ?? 'Not available') ?></li>
      <li>Region: <?= htmlspecialchars($result['region'] ?? 'Not available') ?></li>
      <li>Latitude: <?= htmlspecialchars((string)($result['latitude'] ?? '')) ?></li>
      <li>Longitude: <?= htmlspecialchars((string)($result['longitude'] ?? '')) ?></li>
    </ul>
    <p>Postcode data from postcodes.io.</p>
  <?php endif; ?>
</main>
</body>
</html>