<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/queries.php';
requireLogin();

$jobs = getEngineerSchedule($pdo, (int)$_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My schedule</title>
  <link rel="stylesheet" href="css/ux.css">
</head>
<body>
<main>
  <h1>My schedule</h1>
  <?php if (empty($jobs)): ?>
    <p>No jobs scheduled.</p>
  <?php else: ?>
    <table>
      <thead><tr><th scope="col">When</th><th scope="col">Ticket</th><th scope="col">Site</th><th scope="col">Brief</th></tr></thead>
      <tbody>
      <?php foreach ($jobs as $job): ?>
        <tr>
          <td><?= htmlspecialchars($job['scheduled_start']) ?></td>
          <td><?= htmlspecialchars($job['ticket_title']) ?></td>
          <td><?= htmlspecialchars($job['site_postcode']) ?></td>
          <td><?= htmlspecialchars($job['job_brief'] ?? '') ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</main>
</body>
</html>